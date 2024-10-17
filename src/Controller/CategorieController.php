<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/categorie')]
class CategorieController extends AbstractController
{

    // controller for the categorie page
    public function __construct(
        private CategorieRepository $categorieRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/', name: 'app_categorie')]
    public function index(SessionInterface $sessionInterface): Response
    {
       

        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
            'titreBouton' => 'Ajouter une catégorie',
            'actionButton' => 'app_categorie_ajouter',
            'entityType' => 'categorie',
            'titre' => 'Liste des catégories',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }

    // ajout d'un produit 
    #[Route('/ajouter', name: 'app_categorie_ajouter', methods: ['GET', 'POST'])]
    public function ajouter(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupCategorie = $this->categorieRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);
           
            if ($recupCategorie && $form->getData()->getNom() === $recupCategorie->getNom()) {
                notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Cette catégorie existe déjà');    
                return $this->redirectToRoute('app_categorie_ajouter');
            }

            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addSuccess('Catégorie '. $categorie->getNom() . ' ' . ' ajoutée avec succès');

            return $this->redirectToRoute('app_categorie');
        }

        

        return $this->render('categorie/ajouter.html.twig', [
            'titre' => 'Ajouter une catégorie ',
            'form' => $form->createView(),
        ]);
    }

    // editer une carte 
    #[Route('/editer/{id}', name: 'app_categorie_editer', methods: ['GET', 'POST'])]
    public function editer(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupCategorie = $this->categorieRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);

            if ($recupCategorie) {
                if ($recupCategorie->getId() != $categorie->getId()) {
                    notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addError('Cette categorie existe déjà');    
                    return $this->redirectToRoute('app_categorie_editer', ['id' => $categorie->getId()]);
                }
            }

            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addInfo('Catégorie '. $categorie->getNom() . ' ' . ' modifiée avec succès');

            return $this->redirectToRoute('app_categorie');
        }

        return $this->render('categorie/ajouter.html.twig', [
            'titre' => 'Editer une catégorie ',
            'form' => $form->createView(),
        ]);
    }
    
    // supprimer une carte simplement en post 
    #[Route('/supprimer/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function supprimer(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($categorie);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Catégorie '. $categorie->getNom() . ' ' . ' a été supprimée');
        }
        return $this->redirectToRoute('app_categorie');
    }
}
