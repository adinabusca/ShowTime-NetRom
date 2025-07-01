<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Festival;
use App\Entity\Purchase;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PurchaseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 15; $i++) {
            $userRef = 'user' . rand(1, 20) . '@example.com';       // Assuming 20 users
            $festivalRef = 'Festival ' . rand(1, 10); // Assuming 10 festivals

            $user = $this->getReference($userRef, User::class);
            $festival = $this->getReference($festivalRef,  Festival::class);

            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setFestival($festival);

            $manager->persist($purchase);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            FestivalFixtures::class,
        ];
    }
}
