<?php

namespace App\DataFixtures;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArtistFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $genres = ['Rock', 'Pop', 'Jazz', 'Classical', 'Hip Hop', 'Electronic'];

        for ($i = 1; $i <= 15; $i++) {
            $artist = new Artist();
            $artist->setName("Artist $i");
            $artist->setMusicGenre($genres[array_rand($genres)]);

            $manager->persist($artist);
            $this->addReference('Artist ' . $i, $artist);
        }
        $manager->flush();
    }
}
