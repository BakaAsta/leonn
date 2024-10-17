<?php

namespace App\Controller;

use App\Form\GestionInventaireType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GestionInventaireController extends AbstractController
{
    #[Route('/gestion/inventaire', name: 'app_gestion_inventaire')]
    public function index(Request $request, ProduitRepository $produitRepository): Response
    {
        $form = $this->createForm(GestionInventaireType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->get('reference')->getData();

            $produitRefInterne = $produitRepository->findOneBy(['refInterne' => $ref]);
            $produitRefFabricant = $produitRepository->findOneBy(['refFabricant' => $ref]);

            // dd($produitRefInterne, $produitRefFabricant);


            // le produit existe en refInterne et c un prêt pas de duplication possible
            if (isset($produitRefInterne) && !(in_array('Immo', $produitRefInterne->getTypeProduit()->toArray()))) {
                notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Le produit de ref ' . $ref . ' existe déjà');

                return $this->redirectToRoute('app_produit');
            }
            // le produit existe et il est immmo
            else if (
                (isset($produitRefFabricant) && 
                (in_array('Immo', $produitRefFabricant->getTypeProduit()->toArray()))) ||
                (isset($produitRefInterne) && in_array('Immo', $produitRefInterne->getTypeProduit()->toArray()))
            ) { 
                return $this->render('produit/gestionQuantite.html.twig', [
                    'id' => ($produitRefFabricant ? $produitRefFabricant->getId() : $produitRefInterne) ? ($produitRefFabricant ? $produitRefFabricant->getId() : $produitRefInterne->getId()) : null,
                ]);
            }

            else if (
                (isset($produitRefFabricant) && 
                 in_array('Pret', $produitRefFabricant->getTypeProduit()->toArray()) && 
                 !in_array('Immo', $produitRefFabricant->getTypeProduit()->toArray())
                ) || 
                (isset($produitRefInterne) && 
                 in_array('Pret', $produitRefInterne->getTypeProduit()->toArray()) && 
                 !in_array('Immo', $produitRefInterne->getTypeProduit()->toArray())
                )
            ) {
                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000)
                    ->addInfo("Le produit existe et n'est pas modifiable sur la quantité");
                
                return $this->redirectToRoute('app_produit');
            }
            
            return $this->redirectToRoute('app_produit_ajouter_flow_ref', [
                'refFabricant' => $ref
            ]);

        }

        return $this->render('gestion_inventaire/index.html.twig', [
            'titre' => "Gestion de l'inventaire",
            'controller_name' => 'GestionInventaireController',
            'form' => $form->createView(),
        ]);
    }
}
