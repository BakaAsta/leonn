<?php

namespace App\DataFixtures;

use App\Entity\Marque as EntityMarque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Marque extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // fais un tbaleau de marque connu en informatique 

        $listeMarque = [
            'Acer',
            'Apple',
            'Asus',
            'Dell',
            'HP',
            'Lenovo',
            'MSI',
            'Samsung',
            'Sony',
            'Toshiba',
            'Autre'
        ];

        foreach ($listeMarque as $key => $value) {
            $marque = new EntityMarque();
            $marque->setNom($value);
            $manager->persist($marque);
            $this->addReference($key, $marque);
        }

        $manager->flush();
    }
}
