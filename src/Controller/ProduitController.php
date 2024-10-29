<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\StepForm\CreateProduitFlow;
use App\Form\StepForm\UpdateProductQuantity;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function PHPSTORM_META\map;

#[Route('/produit')]
class ProduitController extends AbstractController 
{
    public function __construct(
        private readonly ProduitRepository $produitRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly JWTTokenManagerInterface $jwtManager
    )
    {}

    #[Route('/', name: 'app_produit')]
    #[IsGranted('ROLE_USER')]
    public function index(SessionInterface $sessionInterface): Response
    {
        $jwt = $this->jwtManager->create($this->getUser());
        $sessionInterface->set('accessToken', $jwt);
        $token = $sessionInterface->get('accessToken');
        
        return $this->render('produit/index.html.twig', [
            'titre' => 'Liste des produits',
            'actionButton' => 'app_produit_ajouter_flow',
            'titreBouton' => 'Ajouter un produit',
            'accessToken' => $token,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/ajouter', name: 'app_produit_ajouter_flow', methods: ['GET', 'POST'])]
    #[Route('/ajouter/{refFabricant}', name: 'app_produit_ajouter_flow_ref', methods: ['GET', 'POST'])]
    public function ajouterFlow(CreateProduitFlow $flow, ?string $refFabricant): Response {
        $date = new \DateTime();
        $produit = new Produit();
        $produit->setRefInterne($date->format('dmY') . '00' . $this->produitRepository->nextId());
        $produit->setEtat('Disponible');
        // dd($produit->getRefInterne());
        $produit->setQuantite(1);
        if ($refFabricant) {
            $produit->setRefFabricant($refFabricant);
        }

        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($produit);
        $form = $flow->createForm();
        
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            
            if ($flow->nextStep()) {
                $form = $flow->createForm();
                
            } else {
                
                $produitRefFrabicantImmo = $this->produitRepository->findOneBy(['refFabricant' => $produit->getRefFabricant()]);
                
                if (isset($produitRefFrabicantImmo) && (in_array('Immo', $produitRefFrabicantImmo->getTypeProduit()->toArray()))) {
                    notyf()->position('x', 'center')->position('y', 'top')->duration(4000)->addInfo('Le produit existe déja');
                
                    return $this->redirectToRoute('app_gestion_inventaire', [
                        'id' => $produitRefFrabicantImmo->getId()
                    ]);
                }

                // on va dupliquer le produit en fonction du nombre de quantité
                $quantite = $produit->getQuantite();

                if (in_array('Immo', $produit->getTypeProduit()->toArray())) {
                    $this->entityManager->persist($produit);
                    $this->entityManager->flush();  
                }

                else {
                    $produit->setQuantite(1);
                    $this->entityManager->persist($produit);
                    $this->entityManager->flush();
                    for ($i = 0; $i < $quantite - 1; $i++) {
                        $produitDuplique = new Produit();
                        $produitDuplique->setNom($produit->getNom());
                        $produitDuplique->setRefInterne($date->format('dmY') . '00' . $this->produitRepository->nextId());
                        $produitDuplique->setRefFabricant($produit->getRefFabricant());
                        if (count($produit->getTypeProduit()) > 0) {
                            for ($j = 0; $j < count($produit->getTypeProduit()); $j++) {
                                $produitDuplique->addTypeProduit($produit->getTypeProduit()[$j]);
                            }
                        }
                        if ($produit->getMarque() != null) {
                            $produitDuplique->setMarque($produit->getMarque());
                        } 
                        if (count($produit->getCategories()) > 0) {
                            for ($j = 0; $j < count($produit->getCategories()); $j++) {
                                $produitDuplique->addCategory($produit->getCategories()[$j]);
                            }
                        }
                        $produitDuplique->setQuantite(1);
                        $produitDuplique->setUpdatedAt(new \DateTime());

                        $this->entityManager->persist($produitDuplique);
                        $this->entityManager->flush();
                        notyf()
                        ->position('x', 'center')
                        ->position('y', 'top')
                        ->duration(4000) // 2 secondes
                        ->addSuccess('Produit '. $produitDuplique->getRefInterne() . ' ajouté avec succès');
                    }
                }

                $flow->reset(); // Supprime les données d'étape de la session

                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 secondes
                    ->addSuccess('Produit '. $produit->getRefInterne() . ' ajouté avec succès');

                return $this->redirectToRoute('app_produit');
            }
        }
        
        // dd($form->createView(), $flow);
        return $this->render('produit/ajouter_flow.html.twig', [
            'titre' => 'Ajouter un produit ',
            'form' => $form->createView(),
            'flow' => $flow,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/incremente/quantite/{id}', name: 'app_produit_incremente_quantite', methods: ['GET', 'POST'])] 
    #[Route('/decremente/quantite/{id}', name: 'app_produit_decremente_quantite', methods: ['GET', 'POST'])] 
    public function editerQuantiteFlow(Produit $produit, Request $request, UpdateProductQuantity $flow): Response 
    {
        $produit->setUpdatedAt(new \DateTime());
        $quantiteProduit = $produit->getQuantite();
        $route = $request->attributes->get('_route');

        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($produit);
        $form = $flow->createForm();


        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);
            
            if ($flow->nextStep()) {
                // Formulaire pour l'étape suivante
                $form = $flow->createForm();
                
            } else {
            
            // $quantite = $form->get('quantite')->getData();
            $quantite = ($flow->getFormData())->getQuantite();

            if ($route == 'app_produit_incremente_quantite') {
                // dd($produit->getQuantite(), $quantite);
                $produit->setQuantite($quantiteProduit + (int)$quantite);
            }
            else if ($route == 'app_produit_decremente_quantite') {
                if ($quantiteProduit - (int)$quantite < 0) {
                    $produit->setQuantite(0);
                }
                else {
                    $produit->setQuantite($quantiteProduit - (int)$quantite);
                }
            }

            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addInfo('Le produit de référence ' . $produit->getRefFabricant() . 'a une quantité de ' . $produit->getQuantite());

            return $this->redirectToRoute('app_produit');
        }
    }
        return $this->render('produit/editer_quantite.html.twig', [
            'titre' => 'Editer la quantité ',
            'form' => $form->createView(),
            'quantiteActuelle' => $quantiteProduit,
            'flow' => $flow,
            'route' => $route
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/editer/{id}', name: 'app_produit_editer', methods: ['GET', 'POST'])]
    public function editer(Produit $produit, CreateProduitFlow $flow): Response
    {
        $produit->setUpdatedAt(new \DateTime());
        // $form = $this->createForm(ProduitType::class, $produit);
        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($produit);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {

                if (in_array('Immo', $produit->getTypeProduit()->toArray())) {
                    // dd($produit);
                    $this->entityManager->flush();
                }

                else {
            
                // on va dupliquer le produit en fonction du nombre de quantité
                $quantite = $produit->getQuantite();
                $date = new \DateTime();

                $produit->setQuantite(1);
                $this->entityManager->persist($produit);
                $this->entityManager->flush();
                
                for ($i = 0; $i < $quantite; $i++) {
                    $produitDuplique = new Produit();
                    $produitDuplique->setNom($produit->getNom());
                    $produitDuplique->setRefInterne($date->format('dmY') . '00' . $this->produitRepository->nextId());
                    $produitDuplique->setRefFabricant($produit->getRefFabricant());
                    if (count($produit->getTypeProduit()) > 0) {
                        for ($j = 0; $j < count($produit->getTypeProduit()); $j++) {
                            $produitDuplique->addTypeProduit($produit->getTypeProduit()[$j]);
                        }
                    }
                    if ($produit->getMarque() != null) {
                        $produitDuplique->setMarque($produit->getMarque());
                    } 
                    if (count($produit->getCategories()) > 0) {
                        for ($j = 0; $j < count($produit->getCategories()); $j++) {
                            $produitDuplique->addCategory($produit->getCategories()[$j]);
                        }
                    }
                    $produitDuplique->setQuantite($produit->getQuantite());
                    $produitDuplique->setUpdatedAt(new \DateTime());

                    $this->entityManager->persist($produitDuplique);
                    $this->entityManager->flush();
                    notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 secondes
                    ->addSuccess('Produit '. $produitDuplique->getRefInterne() . ' ajouté avec succès');
                }
            }

                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addInfo('Produit '. $produit->getNom() . ' ' . ' modifié avec succès');

                return $this->redirectToRoute('app_produit');
            }
        }

        return $this->render('produit/ajouter_flow.html.twig', [
            'titre' => 'Editer un produit ',
            'form' => $form->createView(),
            'refInterne' => $produit->getRefInterne(),
            'flow' => $flow,
        ]);
    }

    //supprimer un produit

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/supprimer/{id}', name: 'app_produit_delete', methods: ['POST'])]
    public function supprimer(Request $request, Produit $produit): Response
    {
       if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($produit);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Produit '. $produit->getNom() . ' ' . ' a été supprimé');
        }

        return $this->redirectToRoute('app_produit');
    }
}
