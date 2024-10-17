<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeProduit extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // crée une liste avec seulement Immo et pret 
        $types = ['Immo', 'Pret'];

        foreach ($types as $type) {
            $typeProduit = new \App\Entity\TypeProduit();
            $typeProduit->setNom($type);
            $manager->persist($typeProduit);
            $this->addReference($type, $typeProduit);
        }
        $manager->flush();
    }
}
