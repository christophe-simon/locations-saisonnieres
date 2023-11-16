<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Picture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 1; $i <= 30; $i++) {
            $ad = new Ad;
            
            $title = $faker->sentence();
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $coverPicture = $faker->imageUrl(1000, 350);

            $ad
                ->setTitle($title)
                ->setPrice(mt_rand(40, 200))
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setCoverPicture($coverPicture)
                ->setRooms(mt_rand(1, 5))
            ;

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $picture = new Picture;

                $picture
                    ->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad)
                ;

                $manager->persist($picture);
            }
    
            $manager->persist($ad);
        }

        $manager->flush();
    }
}
