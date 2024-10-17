<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/marque')]
class MarqueController extends AbstractController
{

    //construct

    public function __construct(
        private MarqueRepository $marqueRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/', name: 'app_marque')]
    public function index(SessionInterface $sessionInterface): Response
    {
       

        return $this->render('marque/index.html.twig', [
            'controller_name' => 'MarqueController',
            'titreBouton' => 'Ajouter une marque',
            'actionButton' => 'app_marque_ajouter',
            'entityType' => 'marque',
            'urlModif' => 'app_marque_editer',
            'urlSupp' => 'app_marque_delete',
            'titre' => 'Liste des marques',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }

    // ajout d'une marque 
    #[Route('/ajouter', name: 'app_marque_ajouter', methods: ['GET', 'POST'])]
    public function ajouter(Request $request): Response
    {
        $marque = new Marque();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupMarque = $this->marqueRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);
           
            if ($recupMarque) {
                notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Cette marque existe déjà');    
                return $this->redirectToRoute('app_marque_ajouter');
            }

            $this->entityManager->persist($marque);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addSuccess('La marque ' . $marque->getNom() . ' a été ajoutée');

            return $this->redirectToRoute('app_marque');
        }

        

        return $this->render('marque/ajouter.html.twig', [
            'titre' => 'Ajouter une marque ',
            'form' => $form->createView(),
        ]);
    }

    // editer un produit 
    #[Route('/editer/{id}', name: 'app_marque_editer', methods: ['GET', 'POST'])]
    public function editer(Request $request, Marque $marque): Response
    {
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupMarque = $this->marqueRepository->findOneBy(['nom' =>  $form->getData()->getNom()]);

            if ($recupMarque){
                if ($recupMarque->getId() != $marque->getId()) {
                    notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(2000) // 2 seconds
                    ->addError('Cette marque existe déjà');    
                    return $this->redirectToRoute('app_marque_editer', ['id' => $marque->getId()]);
                }
            }
            $this->entityManager->flush();

            notyf()
            ->position('x', 'center')
            ->position('y', 'top')
            ->duration(4000) // 2 seconds
            ->addInfo('La marque ' . $marque->getNom() . ' a été modifiée');

            return $this->redirectToRoute('app_marque');
        }

        

        return $this->render('marque/ajouter.html.twig', [
            'titre' => 'Editer une marque ',
            'form' => $form->createView(),
        ]);
    }   

    // supprimer une marque

    #[Route('/supprimer/{id}', name: 'app_marque_delete', methods: ['POST'])]
    public function supprimer(Request $request, Marque $marque): Response
    {
        if ($this->isCsrfTokenValid('delete' . $marque->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($marque);
            $this->entityManager->flush();

            notyf()
            ->position('x', 'center')
            ->position('y', 'top')
            ->duration(4000) // 2 seconds
            ->addError('La marque ' . $marque->getNom() .' a été supprimée');
        }

        return $this->redirectToRoute('app_marque');
    }
}
