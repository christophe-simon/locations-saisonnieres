<?php

namespace App\Controller;

use App\Service\Stats;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(Stats $stats): Response
    {
        $statistics = $stats->getStats();
        $bestAds = $stats->getAds('DESC');
        $worstAds = $stats->getAds('ASC');

        return $this->render('admin/dashboard/index.html.twig', [
            'statistics' => $statistics,
            'bestAds' => $bestAds,
            'worstAds' => $worstAds
        ]);
    }
}
