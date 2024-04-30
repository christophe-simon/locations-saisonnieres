<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Enables to display the ads
     *
     * @param AdRepository $repo
     * @return Response
     */
    #[Route('/admin/ads', name: 'app_admin_ads_index')]
    public function index(AdRepository $repo): Response
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll(),
        ]);
    }

    /**
     * Enables to update an ad
     *
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/ad/update/{id}', name: 'app_admin_ad_update')]
    public function update(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été validée"
            );
        }

        return $this->render('admin/ad/update.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Enables to delete an ad
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/ad/delete/{id}', name: 'app_admin_ad_delete')]
    public function delete(Ad $ad, EntityManagerInterface $manager): Response
    {
        if (count($ad->getBookings()) > 0) {
            $this->addFlash(
                'danger',
                "L'annonce <strong>{$ad->getTitle()}</strong> ne peut pas être supprimée si elle possède déjà des réservations"
            );
        } else {
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
            );
        }

        return $this->redirectToRoute('app_admin_ads_index');
    }
}
