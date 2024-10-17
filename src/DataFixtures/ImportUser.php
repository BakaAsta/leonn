<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportUser extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher
        )
    {

    }


    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('guillaume.carrio@ecirtp.fr');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                '123456'
            )
        );

        $userLalala = new User();
        $userLalala->setEmail('lalala@ecirtp.fr');
        $userLalala->setRoles(['ROLE_ADMIN']);
        $userLalala->setPassword(
            $this->userPasswordHasher->hashPassword(
                $userLalala,
                '123456'
            )
        );

        $manager->persist($user);
        $manager->persist($userLalala); 

        for($i=0; $i<100; $i++){
            $user = new User();
            $user->setEmail('a@b.'.$i);
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    '123456'
                )
            );
            $manager->persist($user);
        }
        $manager->flush();
    }
}
