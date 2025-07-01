<?php

namespace App\DataFixtures;

use App\Entity\Userdetails;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserdetailsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
//        // $product = new Product();
//        // $manager->persist($product);
//
        //$userRepository = $manager->getRepository(User::class);
        for ($i = 1; $i <= 20; $i++) {

            $user = $this->getReference( 'user' . $i .'@example.com', User::class,);

            $details = new Userdetails();
            $details->setFirstName("First{$i}");
            $details->setLastName("Last{$i}");
            $details->setAge(20 + $i);
            $roles = ['ADMIN', 'CUSTOMER'];
            $details->setRole($roles[array_rand($roles)]);

            // Set bi-directional relation
            $details->setUser($user);
            $user->setUserDetails($details);

            $manager->persist($details);
        }

        $manager->flush();
    }

     public function getDependencies(): array
     {
         return [UserFixtures::class];
     }


}
