<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;

class AdController extends AbstractController
{
    /**
     * This method enables to display the ads
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/ads', name: 'app_ads_index')]
    public function index(AdRepository $repo): Response
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * This method enables to create a new ad
     *
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ad/create', name: 'app_ad_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $ad = new Ad;

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getPictures() as $picture) {
                $picture->setAd($ad);
                $manager->persist($picture);
            }

            $ad->setManager($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong> " . $ad->getTitle() . " </strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('app_ad_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This method enables to display a given ad according to its slug
     *
     * @param Ad $ad
     * @return Response
     */
    #[Route('/ad/show/{slug}', name: 'app_ad_show')]
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    /**
     * This method enables to update a given ad according to its slug
     *
     * @IsGranted("ROLE_USER")
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ad/update/{slug}', name: 'app_ad_update')]
    #[IsGranted('ROLE_USER')]
    public function update(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if ($user !== $ad->getManager()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à modifier cette annonce.");
        }

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($ad->getPictures() as $picture) {
                $picture->setAd($ad);
                $manager->persist($picture);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce ont bien été enregistrées"
            );

            return $this->redirectToRoute('app_ad_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/update.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * This method enables to delete a given ad according to its slug
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ad/delete/{slug}', name: 'app_ad_delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Ad $ad, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        if ($user !== $ad->getManager()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas autorisé à supprimer cette annonce.");
        }

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('app_ads_index');
    }
}
