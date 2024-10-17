<?php

namespace App\Controller;

use App\Entity\TypeProduit;
use App\Form\TypeProduitType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TypeProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/typeProduit')]
class TypeProduitController extends AbstractController
{
    public function __construct(
        private TypeProduitRepository $typeProduitRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/', name: 'app_type_produit')]
    public function index(SessionInterface $sessionInterface): Response
    {
      

        return $this->render('type_produit/index.html.twig', [
            'controller_name' => 'TypeProduitController',
            'titreBouton' => 'Ajouter un type de produit',
            'actionButton' => 'app_type_produit_ajouter',
            'entityType' => 'type_produit',
            'titre' => 'Liste des types de produit',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }

    //ajouter un type de produit
    #[Route('/ajouter', name: 'app_type_produit_ajouter', methods: ['GET', 'POST'])]
    public function ajouter(Request $request): Response
    {
        $typeProduit = new TypeProduit();
        $form = $this->createForm(TypeProduitType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupTypeProduit = $this->typeProduitRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);

            if ($recupTypeProduit && $form->getData()->getNom() === $recupTypeProduit->getNom()) {
                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addError('Ce type de produit existe déjà');
                return $this->redirectToRoute('app_type_produit_ajouter');
            }

            $this->entityManager->persist($typeProduit);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addSuccess('Type de produit' . ' ' . $typeProduit->getNom() . ' ' . 'ajouté avec succès');

            return $this->redirectToRoute('app_type_produit');
        }

        return $this->render('type_produit/ajouter.html.twig', [
            'titre' => 'Ajouter un type de produit ',
            'form' => $form->createView(),
        ]);
    }

    // editer un type de produit 
    #[Route('/editer/{id}', name: 'app_type_produit_editer', methods: ['GET', 'POST'])]
    public function editer(Request $request, TypeProduit $typeProduit): Response
    {
        $form = $this->createForm(TypeProduitType::class, $typeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupTypeProduit = $this->typeProduitRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);

            if ($recupTypeProduit && $form->getData()->getNom() === $recupTypeProduit->getNom() && $recupTypeProduit->getId() != $typeProduit->getId()){
                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addError('Ce type de produit existe déjà');
                return $this->redirectToRoute('app_type_produit_editer', ['id' => $typeProduit->getId()]);
            }

            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addInfo('Type de produit' . ' ' . $typeProduit->getNom() . ' ' . 'modifié avec succès');

            return $this->redirectToRoute('app_type_produit');
        }

        return $this->render('type_produit/ajouter.html.twig', [
            'titre' => 'Editer un type de produit ',
            'form' => $form->createView(),
        ]);
    }

    // supprimer un type de produit
    #[Route('/supprimer/{id}', name: 'app_type_produit_delete', methods: ['POST'])]
    public function delete(Request $request, TypeProduit $typeProduit): Response
    {
        if ($this->isCsrfTokenValid('delete' . $typeProduit->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($typeProduit);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Type de produit' . ' ' . $typeProduit->getNom() . ' ' . 'a été supprimé');
        }

        return $this->redirectToRoute('app_type_produit', [], Response::HTTP_SEE_OTHER);
    }
    
}
