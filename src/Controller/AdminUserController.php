<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users/{page}', name: 'app_admin_users_index', requirements: ['page' => "\d+"], defaults: ['page' => 1])]
    public function index(int $page, Pagination $pagination): Response
    {
        $pagination
            ->setEntityClass(User::class)
            ->setCurrentPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
