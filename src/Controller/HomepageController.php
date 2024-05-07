<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * This method enables to display the homepage
     *
     * @return Response
     */
    #[Route('/', name: 'app_homepage')]
    public function index(AdRepository $adRepo, UserRepository $userRepo): Response
    {
        return $this->render('homepage/index.html.twig', [
            'ads' => $adRepo->findBestAds(3),
            'users' => $userRepo->findBestUsers(2),
        ]);
    }
}
