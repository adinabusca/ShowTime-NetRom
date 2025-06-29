<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Userdetails;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;



class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail("user{$i}@example.com");
            $user->setPassword('password' . $i);
            $user->setToken(bin2hex(random_bytes(16)));


            $manager->persist($user);

        }


        $manager->flush();
    }


}
