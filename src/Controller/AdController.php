<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    #[Route('/ads', name: 'app_ads_index')]
    public function index(AdRepository $repo): Response
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    #[Route('/ad/create', name: 'app_ad_create')]
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

    #[Route('/ad/show/{slug}', name: 'app_ad_show')]
    public function show(Ad $ad): Response
    {
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    #[Route('/ad/update/{slug}', name: 'app_ad_update')]
    public function update(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
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
}
