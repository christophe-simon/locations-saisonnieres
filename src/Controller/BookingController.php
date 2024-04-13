<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    /**
     * This method enables to create a booking
     *
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/booking/create/{slug}', name: 'app_booking_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $booking
                ->setBooker($user)
                ->setAd($ad);

            // If dates are not available -> error message
            if (!$booking->isABookablePeriod()) {
                $this->addFlash(
                    'danger',
                    "Les dates choisies ne peuvent être réservées, elles sont déjà prises"
                );
            } else {
                // If dates are available -> booking and redirection
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('app_booking_show', [
                    'id' => $booking->getId(),
                    'withAlert' => true
                ]);
            }
        }

        return $this->render('booking/create.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * This method enables to show a booking
     *
     * @param Booking $booking
     * @return Response
     */
    #[Route('/booking/show/{id}', name: 'app_booking_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Booking $booking): Response
    {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }
}
