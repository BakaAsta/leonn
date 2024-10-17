<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Form\CarteType;
use App\Form\StepForm\CreateCarteFlow;
use App\Service\QwertyToAzerty;
use App\Repository\CarteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// #[IsGranted('ROLE_ADMIN')]
#[Route('/carte')]
class CarteController extends AbstractController
{
    // contruct
    public function __construct(
        private CarteRepository $carteRepository,
        private EntityManagerInterface $entityManager,
        private QwertyToAzerty $qwertzToAzerty
    )
    {
    }
    
    #[Route('/', name: 'app_carte')]
    public function index(SessionInterface $sessionInterface): Response
    {
        $user = $this->getUser() ? $this->getUser()->getRoles() : null;
        if ( $user === null || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            // $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Vous n\'avez pas les droits pour accéder à cette page');    
            
            return $this->redirectToRoute('app_home');
        }
        
        $carte = $this->carteRepository->findAll();
        return $this->render('carte/index.html.twig', [
            'controller_name' => 'CarteController',
            'titreBouton' => 'Ajouter une carte',
            'actionButton' => 'app_carte_ajouter',
            'entityType' => 'carte',
            'titre' => 'Liste des cartes',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }

    // ,ajouter une carte
    #[Route('/ajouter', name: 'app_carte_ajouter', methods: ['GET', 'POST'])]
    #[Route('/ajouter/pret/{valeurCarte}', name: 'app_carte_ajouter_pret', methods: ['GET', 'POST'])]
    public function ajouter(Request $request, ?string $valeurCarte, CreateCarteFlow $flow): Response
    {
        $carte = new Carte();
        // récupérer l'id suivant de la base de données 

        $date = new \DateTime();
        $carte->setReference($date->format('dmY') . '00' . $this->carteRepository->nextId());

        // $form = $this->createForm(CarteType::class, $carte);
        if ($valeurCarte) {
            $carte->setValeur($valeurCarte);
        }
        
        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($carte);
        $form = $flow->createForm();
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
                
            } else {

            $recupCarteParValeur = $this->carteRepository->findOneBy(['valeur' => $this->qwertzToAzerty->convertAzertyToQwerty($carte->getValeur())]);
            
            $recupCarteParRef = $this->carteRepository->findOneBy(['reference' => $this->qwertzToAzerty->convertAzertyToQwerty($carte->getReference())]);

            if ($recupCarteParValeur || $recupCarteParRef) {
                // dd('test');
                notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('Cette carte existe déjà');    
                return $this->redirectToRoute('app_carte_ajouter');
            }
            if ($valeurCarte) {
                $carte->setValeur($this->qwertzToAzerty->convertAzertyToQwerty($valeurCarte));
                $this->entityManager->persist($carte);
                $this->entityManager->flush();
                return $this->redirectToRoute('app_pret_ajouter_carte', [
                    'carte' => $carte->getId(),
                ]);
            }
            $carte->setValeur($this->qwertzToAzerty->convertAzertyToQwerty($carte->getValeur()));
            $this->entityManager->persist($carte);
            $this->entityManager->flush();
            
            return $this->redirectToRoute('app_carte');
        }
    }
        return $this->render('carte/ajouter.html.twig', [
            'titre' => 'Ajouter une carte ',
            'carte' => $carte, 
            'form' => $form->createView(),
            'flow' => $flow
        ]);
    }
    //editer une carte
    #[Route('/editer/{id}', name: 'app_carte_editer', methods: ['GET', 'POST'])]
    public function editer(Request $request, Carte $carte): Response
    {
        $form = $this->createForm(CarteType::class, $carte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recupCarteParValeur = $this->carteRepository->findOneBy(['valeur' => $this->qwertzToAzerty->convertAzertyToQwerty($carte->getValeur())]);
            
            $recupCarteParRef = $this->carteRepository->findOneBy(['reference' => $this->qwertzToAzerty->convertAzertyToQwerty($carte->getReference())]);
            

            if ($recupCarteParValeur || $recupCarteParRef) {
                // si la carte existe déjà
                // je dois vérifier si la carte existe déjà
                if ($recupCarteParValeur) {
                    if ($recupCarteParValeur->getId() != $carte->getId()) {
                        notyf()
                        ->position('x', 'center')
                        ->position('y', 'top')
                        ->duration(4000) // 2 seconds
                        ->addError('Cette carte existe déjà');    
                        return $this->redirectToRoute('app_carte_editer', ['id' => $carte->getId()]);
                    }
                }
                else if ($recupCarteParRef) {
                    if ($recupCarteParRef->getId() != $carte->getId()) {
                        notyf()
                        ->position('x', 'center')
                        ->position('y', 'top')
                        ->duration(4000) // 2 seconds
                        ->addError('Cette carte existe déjà');    
                        return $this->redirectToRoute('app_carte_editer', ['id' => $carte->getId()]);
                    }
                }
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('app_carte');
        }
        return $this->render('carte/ajouter.html.twig', [
            'titre' => 'Editer une carte',
            'carte' => $carte,
            'form' => $form->createView(),
        ]);
    }
    //supprimer une carte
    #[Route('/supprimer/{id}', name: 'app_carte_delete', methods: ['POST'])]
    public function delete(Request $request, Carte $carte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$carte->getId(), $request->getPayload()->get('_token')) && count($carte->getPrets())==0) {
            $entityManager->remove($carte);
            $entityManager->flush();
            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addError('La carte '. $carte->getReference() . ' ' . 'a été supprimée');
        }
        return $this->redirectToRoute('app_carte', [], Response::HTTP_SEE_OTHER);
    }
}

