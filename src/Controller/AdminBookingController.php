<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Enables to display bookings
     *
     * @param BookingRepository $repo
     * @return Response
     */
    #[Route('/admin/bookings', name: 'app_admin_bookings_index')]
    public function index(BookingRepository $repo): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll(),
        ]);
    }

    /**
     * Enables to update a booking
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/booking/update/{id}', name: 'app_admin_booking_update')]
    public function update(Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n° <strong>{$booking->getId()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('app_admin_bookings_index');
        }

        return $this->render('admin/booking/update.html.twig', [
            'form' => $form->createView(),
            'booking' => $booking
        ]);
    }

    /**
     * Enables to delete a booking
     *
     * @param Booking $booking
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/booking/delete/{id}', name: 'app_admin_booking_delete')]
    public function delete(Booking $booking, EntityManagerInterface $manager): Response
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservation faite par <strong>{$booking->getBooker()->getFullName()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('app_admin_bookings_index');
    }
}
