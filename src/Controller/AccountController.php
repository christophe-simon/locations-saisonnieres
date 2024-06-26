<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Form\PersonalDataUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Enables to log in
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'app_account_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $hasError = $error !== null;
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $hasError,
            'username' => $username
        ]);
    }

    /**
     * Enables to log out
     *
     * @return void
     */
    #[Route('/logout', name: 'app_account_logout')]
    public function logout(): void
    {
        // empty
    }

    /**
     * Enables to register
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/register', name: 'app_account_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user, [
            'validation_groups' => ['Default', 'registration']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé'
            );

            return $this->redirectToRoute('app_account_login');
        }

        return $this->render('account/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Enables to update one's personal data
     *
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/account/update-personal-data', name: 'app_account_update_personal_data')]
    #[IsGranted('ROLE_USER')]
    public function updatePersonalData(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(PersonalDataUpdateType::class, $user, [
            'validation_groups' => ['Default']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications du profil utilisateur ont bien été enregistrées"
            );

            return $this->redirectToRoute('app_account_show');
        }

        return $this->render('account/update-personal-data.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Enables to update one's password
     *
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/account/update-password', name: 'app_account_update_password')]
    #[IsGranted('ROLE_USER')]
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $passwordUpdate = new PasswordUpdate();

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate, [
            'validation_groups' => ['Default']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$passwordHasher->isPasswordValid($user, $passwordUpdate->getOldPassword())) {
                $form->get('oldPassword')->addError(new FormError("Vous n'avez pas saisi votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($hashedPassword);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "La modification du mot de passe a bien été enregistrée"
                );

                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('account/update-password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Enables to display one's data
     *
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    #[Route('/account/show', name: 'app_account_show')]
    #[IsGranted('ROLE_USER')]
    public function show(): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * Enables to display one's bookings
     *
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    #[Route('/account/bookings', name: 'app_account_bookings')]
    #[IsGranted('ROLE_USER')]
    public function showBookings(): Response
    {
        return $this->render('account/bookings.html.twig');
    }
}
