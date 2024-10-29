<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\MarqueRepository;
use App\Repository\ProduitRepository;
use App\Repository\TypeProduitRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFaker extends Fixture
{

    public function __construct(
        private readonly CategorieRepository   $categorieRepository,
        private readonly TypeProduitRepository $typeProduitRepository,
        private readonly ProduitRepository $produitRepository
    )
    {
        
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $date = new \DateTime();
        for ($i = 0; $i < 10; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->word);
            $produit->setRefInterne($date->format('dmY') . '00' . $this->produitRepository->nextId());
            $produit->setRefFabricant($faker->word);
            $produit->setEtat('Disponible');
            $produit->addTypeProduit($this->typeProduitRepository->findOneBy(['nom' => 'Immo']));
            $produit->addCategory($this->categorieRepository->findOneBy(['nom' => 'Logiciel']));
            $produit->setCommentaire('test faker');
            $produit->setQuantite($faker->numberBetween(1, 100));

            $manager->persist($produit);
            $manager->flush();
        }
    }
}
