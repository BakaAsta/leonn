<?php

namespace App\Controller;

use App\Entity\Carte;
use App\Entity\Pret;
use App\Form\PretType;
use App\Form\StepForm\CreatePretFlow;
use App\Repository\PretRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use SessionIdInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\VarDumper\Cloner\Data;

#[Route('/pret')]
class PretController extends AbstractController
{


    public function __construct(
        private PretRepository $pretRepository,
        private EntityManagerInterface $entityManager,
    )
    {
    }

    #[Route('/', name: 'app_pret')]
    public function index(SessionInterface $sessionInterface): Response
    {
        return $this->render('pret/index.html.twig', [
            'controller_name' => 'PretController',
            'titreBouton' => 'Ajouter un pret',
            'actionButton' => 'app_pret_ajouter',
            'titre' => 'Liste des prets',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }

    #[Route('/ajouter', name: 'app_pret_ajouter', methods: ['GET', 'POST'])]
    #[Route('/ajouter/carte/{carte}', name: 'app_pret_ajouter_carte', methods: ['GET', 'POST'])]
    public function ajouter(?Carte $carte, CreatePretFlow $flow): Response
    {
        $pret = new Pret();
        if ($carte) {
            $pret->setCarte($carte);
        }

        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($pret);
        $form = $flow->createForm();

        // dd($pret);

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            // dd($pret);

            if ($flow->nextStep()) {
                $form = $flow->createForm();

            } else {

                // dd($pret);

                $produitPret = $pret->getProduit();
                foreach ($produitPret as $produit) {
                    $produit->setEtat('pret');
                }

                $this->entityManager->persist($pret);
                $this->entityManager->flush();

                $flow->reset();

                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addSuccess('Le prêt concernant ' . $pret->getUser() . ' a été ajouté');

                return $this->redirectToRoute('app_pret');
            }
        }

        // dd($flow->getFormData()->getUser());

        return $this->render('pret/ajouter.html.twig', [
            'titre' => 'Ajouter un pret ',
            'carte' => $carte,
            'form' => $form->createView(),
            'flow' => $flow,
        ]);
    }

    // editer un pret

    #[Route('/editer/{id}', name: 'app_pret_editer', methods: ['GET', 'POST'])]
    public function editer(Pret $pret, CreatePretFlow $flow): Response
    {
        foreach ($pret->getProduit() as $produit) {
            $produit->setEtat('Disponible');
        }
        $flow->setAllowDynamicStepNavigation(true);
        $flow->bind($pret);
        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                if ($pret->getDateFin() == null) {
                    $produitPret = $pret->getProduit();
                    foreach ($produitPret as $produit) {
                        $produit->setEtat('pret');
                    }
                }

                $this->entityManager->flush();

                $flow->reset();

                notyf()
                    ->position('x', 'center')
                    ->position('y', 'top')
                    ->duration(4000) // 2 seconds
                    ->addSuccess('Le prêt concernant ' . $pret->getUser() . ' a été modifié');

                return $this->redirectToRoute('app_pret');
            }
        }

        return $this->render('pret/ajouter.html.twig', [
            'titre' => 'Editer un pret ',
            'carte' => $pret->getCarte(),
            'form' => $form->createView(),
            'flow' => $flow,
        ]);
    }

    // supprimer un pret en post

    #[Route('/supprimer/{id}', name: 'app_pret_delete', methods: ['POST'])]
    public function supprimer(Request $request, Pret $pret): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pret->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($pret);
            $this->entityManager->flush();

            notyf()
                ->position('x', 'center')
                ->position('y', 'top')
                ->duration(4000) // 2 seconds
                ->addSuccess('Le prêt numéro '. $pret->getId() . ' concernant ' . $pret->getUser() . ' a été supprimé');
        }

        return $this->redirectToRoute('app_pret');
    }
}
