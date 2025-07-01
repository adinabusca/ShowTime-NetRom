<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use App\Entity\Festival;
use App\Entity\FestivalArtist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FestivalArtistFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 15; $i++) {
            $festivalRef = 'Festival ' . rand(1, 10);
            $artistRef = 'Artist ' . rand(1, 15);


            $festival = $this->getReference($festivalRef, Festival :: class);
            $artist = $this->getReference($artistRef,  Artist :: class);

            $performance = new FestivalArtist();
            $performance->setFestival($festival);
            $performance->setArtist($artist);

            $manager->persist($performance);
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            FestivalFixtures::class,
            ArtistFixtures::class,
        ];
    }
}
