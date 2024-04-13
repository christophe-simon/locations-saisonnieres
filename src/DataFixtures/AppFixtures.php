<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Picture;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $genders = ['male', 'female'];

        // Users
        $users = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();

            $gender = $faker->randomElement($genders);

            $beginningOfTheUrl = 'https://randomuser.me/api/portraits/';
            $fileExtension = '.jpg';
            $pictureId = $faker->numberBetween(0, 99);

            $picture = $beginningOfTheUrl . ($gender === 'male' ? 'men/' : 'women/') . $pictureId . $fileExtension;

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'password' // plain password
            );

            $user
                ->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setPicture($picture)
                ->setIntroduction($faker->sentence)
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>');

            $manager->persist($user);
            $users[] = $user;
        }

        // Ads
        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad;

            $title = $faker->sentence();
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $coverPicture = $faker->imageUrl(1000, 350);
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad
                ->setTitle($title)
                ->setPrice(mt_rand(40, 200))
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setCoverPicture($coverPicture)
                ->setRooms(mt_rand(1, 5))
                ->setManager($user);

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $picture = new Picture();

                $picture
                    ->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($picture);
            }

            // Bookings
            for ($j = 1; $j <= mt_rand(0, 10); $j++) {
                $booking = new Booking();

                $createdAt = new DateTimeImmutable($faker->dateTimeBetween('-6 months')->format('Y-m-d H:i:s'));
                $startsOn = new DateTimeImmutable($faker->dateTimeBetween('-3 months')->format('Y-m-d H:i:s'));
                $duration = mt_rand(3, 10);
                $endsOn = (clone $startsOn)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) - 1)];
                $comment = mt_rand(0, 1) ? $faker->paragraph() : null;

                $booking
                    ->setBooker($booker)
                    ->setAd($ad)
                    ->setStartsOn($startsOn)
                    ->setEndsOn($endsOn)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment);

                $manager->persist($booking);
            }

            $manager->persist($ad);
        }

        // Admin
        $admin = new User();

        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'password' // plain password
        );

        $admin
            ->setFirstName('Christophe')
            ->setLastName('Simon')
            ->setEmail('csimon@symfony.com')
            ->setPassword($hashedPassword)
            ->setRoles(['ROLE_ADMIN'])
            ->setPicture(null)
            ->setIntroduction($faker->sentence)
            ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(3)) . '</p>');

        $manager->persist($admin);

        $manager->flush();
    }
}
