<?php

namespace App\Controller;

use App\Repository\PretRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class GestionRetardPretController extends AbstractController
{

    public function __construct(
        private PretRepository $pretRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    
    // gestion de pret trier par date de fin prévue du plus tard au plus proche 
    
    
    #[Route('/gestion/retard/pret', name: 'app_gestion_retard_pret')]
    public function index(PaginatorInterface $paginator, Request $request, SessionInterface $sessionInterface): Response
    {
        $prets = $this->pretRepository->pretEnRetard();
        $pagination = $paginator->paginate(
            $prets,
            $request->query->getInt('page', 1),
            20
        );

        $liste_th = ['Utilisateur concerné', 'Carte correspondante', 'Date de début', 'Date de fin prévue', 'Liste des produits', 'Liste des relances', 'Actions'];
        $listeAttributs = ['user', 'carte', 'datePret', 'dateFinPrevue','produit', 'relancePrets'];

        return $this->render('gestion_retard_pret/index.html.twig', [
            'controller_name' => 'GestionRetardPretController',
            'listeObjets' => $pagination,
            'entityType' => 'pret',
            'liste_th' => $liste_th,
            'urlAjout' => 'app_relancePret_ajouter',
            'urlEdit' => 'app_relancePret_editer',
            'listeAttributs' => $listeAttributs,
            'titre' => 'Liste des prêts en retards',
            'accessToken' => $sessionInterface->get('accessToken')
        ]);
    }
}
