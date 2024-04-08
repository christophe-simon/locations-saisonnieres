<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Picture;
use App\Entity\User;
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
            $user->setPassword($hashedPassword);

            $user
                ->setFirstName($faker->firstName($gender))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setRoles(['ROLE_USER'])
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
                $picture = new Picture;

                $picture
                    ->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);

                $manager->persist($picture);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
