<?php

namespace App\DataFixtures;

use App\Entity\Categorie as EntityCategorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Categorie extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // crée une liste de categorie sur du matériel informatique 

        $listeCategorie = [
            'Matériel',
            'Logiciel',
            'Réseau',
            'Sécurité',
            'Développement',
            'Base de données',
            'Système d\'exploitation',
            'Bureautique',
            'Autre'
        ];

        foreach ($listeCategorie as $key => $value) {
            $categorie = new EntityCategorie();
            $categorie->setNom($value);
            $manager->persist($categorie);
            $this->addReference($key, $categorie);
        }

    
        $manager->flush();
    }
}
