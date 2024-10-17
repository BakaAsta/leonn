<?php

namespace App\Controller;

use App\Repository\PretRepository;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use IntlDateFormatter;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class HomeController extends AbstractController
{
    // contruct
    public function __construct(
        private UserRepository $userRepository,
        private ProduitRepository $produitRepository,
        private PretRepository $pretRepository,
        private JWTTokenManagerInterface $jwtManager
    )
    {}

    // #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(PaginatorInterface $paginator, Request $request, SessionInterface $sessionInterface): Response
    {
        $users = $this->userRepository->findAll();
        $produits = $this->produitRepository->findAll();
        $prets = $this->pretRepository->findAll();
        $pretsListe = $this->pretRepository->findByActive();
        $pretsMonth = $this->pretRepository->findByThisMonth();
        $pretsYear = $this->pretRepository->findByYear();
        $pretsLastYear = $this->pretRepository->findByLastYear();
        $pretsPreviousYearSameMonth = $this->pretRepository->findByPreviousYear();
        $pretsRetard = $this->pretRepository->findByLate();

        $paginationPretsListe = $paginator->paginate(
            $pretsListe,
            $request->query->getInt('page', 1),
            5
        );

        $paginationRetardListe = $paginator->paginate(
            $pretsRetard,
            $request->query->getInt('page', 1),
            5
        );

        // dd($request->getSession());
        

        $token = $sessionInterface->get('accessToken');

        if (!(isset($token))) {
            $jwt = $this->jwtManager->create($this->getUser());
            $sessionInterface->set('accessToken', $jwt);
            $token = $sessionInterface->get('accessToken');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'users' => count($users),
            'produits' => count($produits),
            'prets' => count($prets),
            'pretsListe' => $paginationPretsListe,
            'nbrPretsListe' => count($pretsListe),
            'pretsMois' => count($pretsMonth),
            'nbrPretsAn' => count($pretsYear),
            'progressAn' => count($pretsLastYear)>0
                ?(round(((count($pretsYear) - count($pretsLastYear)) / count($pretsLastYear)) * 100)).' % par rapport à l\'an dernier'
                :'Aucun prêts l\'an dernier',
            'nbrPretsRetard' => count($pretsRetard),
            'accessToken' => $token,
            'pretsRetard' => $paginationRetardListe,
            'progressMois' => count($pretsPreviousYearSameMonth)>0
                ?(round(((count($pretsMonth) - count($pretsPreviousYearSameMonth)) / count($pretsPreviousYearSameMonth)) * 100)).' % par rapport au mois de '.datefmt_format(datefmt_create('fr_FR',IntlDateFormatter::FULL,IntlDateFormatter::FULL,'America/Los_Angeles',IntlDateFormatter::GREGORIAN,'MMMM'),new \DateTime()).' l\'an dernier'
                :'Aucun prêts le mois de '.datefmt_format(datefmt_create('fr_FR',IntlDateFormatter::FULL,IntlDateFormatter::FULL,'America/Los_Angeles',IntlDateFormatter::GREGORIAN,'MMMM'),new \DateTime()).' l\'an dernier',
        ]);
    }
}
