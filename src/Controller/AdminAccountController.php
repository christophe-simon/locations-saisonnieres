<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * Enables to log in
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/admin/login', name: 'app_admin_account_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $hasError = $error !== null;
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            'hasError' => $hasError,
            'username' => $username
        ]);
    }

    /**
     * Enables to log out
     *
     * @return void
     */
    #[Route('/admin/logout', name: 'app_admin_account_logout')]
    public function logout(): void
    {
        // empty
    }
}
