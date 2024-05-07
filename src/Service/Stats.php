<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class Stats
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersNumber()
    {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getAdsNumber()
    {
        return $this->manager->createQuery('SELECT COUNT(a) FROM App\Entity\Ad a')->getSingleScalarResult();
    }

    public function getBookingsNumber()
    {
        return $this->manager->createQuery('SELECT COUNT(b) FROM App\Entity\Booking b')->getSingleScalarResult();
    }

    public function getCommentsNumber()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Comment c')->getSingleScalarResult();
    }

    public function getStats()
    {
        $users = $this->getUsersNumber();
        $ads = $this->getAdsNumber();
        $bookings = $this->getBookingsNumber();
        $comments = $this->getCommentsNumber();

        return compact('users', 'ads', 'bookings', 'comments');
    }

    public function getAds($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, a.title, a.id, u.firstName, u.lastName, u.picture
            FROM App\Entity\Comment c
            JOIN c.ad a
            JOIN a.manager u
            GROUP BY a
            ORDER BY note ' . $direction
        )
            ->setMaxResults(5)
            ->getResult();
    }
}
