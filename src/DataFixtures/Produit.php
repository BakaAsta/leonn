<?php

namespace App\DataFixtures;

use App\Repository\CategorieRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\MarqueRepository;
use App\Repository\TypeProduitRepository;

class Produit extends Fixture
{
    public function __construct(
        private CategorieRepository $categorieRepository, 
        private MarqueRepository $marqueRepository, 
        private TypeProduitRepository $typeProduitRepository) {
        
    }

    public function load(ObjectManager $manager): void
    {
        // crée une liste de produit
        $listeProduit = [
            'Ordinateur',
            'Smartphone',
            'Tablette',
            'Imprimante',
            'Scanner',
            'Serveur',
            'Switch',
            'Routeur',
            'Câble',
            'Autre'
        ];

        for($i = 0; $i < count($listeProduit); $i++) {
            $produit = new \App\Entity\Produit();
            $produit->setNom($listeProduit[$i]);
            $produit->setRefFabricant('REF_FABRICANT' . $i);
            $produit->setRefInterne('REF_INTERNE' . $i);
            $produit->setQuantite(1);
            // ...

            // ajoute le type produit Immo et Pret
            $categorieInformatique = $this->categorieRepository->findOneBy(['nom' => 'Matériel']);
            $produit->addCategory($categorieInformatique);
            
            $categorieBureau = $this->categorieRepository->findOneBy(['nom' => 'Bureautique']);
            $produit->addCategory($categorieBureau);
            
            $categorieAutre = $this->categorieRepository->findOneBy(['nom' => 'Autre']);
            $produit->addCategory($categorieAutre);

            $marqueAcer = $this->marqueRepository->findOneBy(['nom' => 'Acer']);
            $produit->setMarque($marqueAcer);

            $typeProduitImmo = $this->typeProduitRepository->findOneBy(['nom' => 'Immo']);
            $produit->addTypeProduit($typeProduitImmo);
            $typeProduitPret = $this->typeProduitRepository->findOneBy(['nom' => 'Pret']);
            $produit->addTypeProduit($typeProduitPret);

            $produit->setCommentaire('fixture produit');
            $produit->setRebus(false);
            $manager->persist($produit);
            //$manager->persist($produit);
        }

        $manager->flush();
    }
}
