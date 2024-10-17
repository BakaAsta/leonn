<?php

namespace App\Controller;

use App\Form\GestionPretType;
use App\Service\QwertyToAzerty;
use App\Repository\PretRepository;
use App\Repository\CarteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GestionPretController extends AbstractController
{
     public function __construct(
        private QwertyToAzerty $qwertzToAzerty,
        private PretRepository $pretRepository,
    )
    {
    }
    #[Route('/gestion/pret', name: 'app_gestion_pret')]
    public function index(Request $request, CarteRepository $carteRepository): Response
    {
        $form = $this->createForm(GestionPretType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ref = $form->get('reference')->getData();

            $carte = $carteRepository->findOneBy(['valeur' => $this->qwertzToAzerty->convertAzertyToQwerty($ref)]);

            if (!$carte) {
                return $this->redirectToRoute('app_carte_ajouter_pret', [
                    'valeurCarte'=> $ref,
                ]);
            }

            if ($carte) {
                if (!$carte->getPrets()->isEmpty()) {
                    foreach ($carte->getPrets() as $pret) {
                        $pretId = $pret->getId();
                        return $this->redirectToRoute('app_pret_editer', [
                            'id' => $pretId,
                        ]);
                    }
                } else {
                    return $this->redirectToRoute('app_pret_ajouter_carte', [
                        'carte' => $carte->getId(),
                    ]);
                }
            }

        }

        return $this->render('gestion_pret/index.html.twig', [
            'titre' => 'Nouveau pret',
            'controller_name' => 'GestionPretController',
            'form' => $form->createView(),
        ]);
    }

    // liste des prets d'un user
    #[Route('/listePrets/user/{user}', name: 'app_user_listePrets', methods: ['GET'])] 
    public function listePrets(SessionInterface $sessionInterface): Response
    {
        return $this->render('user/listePrets.html.twig', [
            'controller_name' => 'PretController',
            'titre' => 'Liste de mes prets',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }
}
