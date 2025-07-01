<?php

namespace App\DataFixtures;

use App\Entity\Festival;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FestivalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $locations = ['New York', 'Los Angeles', 'Chicago', 'Miami', 'Austin'];

        for ($i = 1; $i <= 10; $i++) {
            $festival = new Festival();
            $festival->setName("Festival $i");
            $festival->setLocation($locations[array_rand($locations)]);

            $startDate = new \DateTime('now');
            $festival->setStartDate($startDate);

            // For example, make the festival last 3 days
            $endDate = (clone $startDate)->modify('+2 days');
            $festival->setEndDate($endDate);

            $festival->setPrice(mt_rand(10, 100) . '.00');

            $manager->persist($festival);
            $this->addReference('Festival ' . $i, $festival);
        }

        $manager->flush();
    }
}
