<?php

namespace App\Controller;

use App\Entity\Pret;
use App\Entity\RelancePret;
use App\Service\Envoidemail;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RelancePretRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RelancePretController extends AbstractController
{

    public function __construct(
        private Envoidemail $envoidemail,
        private EntityManagerInterface $entityManager,
        private RelancePretRepository $relancePretRepository,
    )
    {
    }

    #[Route('/relance', name: 'app_relance')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        // liste th avec titre contenu et action
        $liste_th = ['Titre', 'Contenu'];
        $listeAttributs = ['titre', 'contenu'];
        $relancePret = $this->relancePretRepository->findAll();

        $pagination = $paginator->paginate(
            $relancePret,
            $request->query->getInt('page', 1),
            20
        );
        
        return $this->render('relance/index.html.twig', [
            'controller_name' => 'RelanceController',
            'listeObjets' => $pagination,
            'entityType' => 'relancePret',
            'liste_th' => $liste_th,
            'listeAttributs' => $listeAttributs,
            'titre' => 'Historique des relances de prêts',
        ]);
    }

    // ajout d'une relance 
    #[Route('/relance/ajouter/{idPrets}', name: 'app_relancePret_ajouter', methods: ['GET', 'POST'])]
    public function ajouterRelance(Request $request,Pret $idPrets): Response
    {
        $relancePret = new RelancePret();
        $relancePret->setCreatedAt(new \DateTimeImmutable());
        $relancePret->setPret($idPrets);

        // requette qui compte le nombre de relance du pret 
        $relanceIdPret = $this->entityManager->getRepository(RelancePret::class)->findBy(['pret' => $idPrets]);

        $relancePret->setTitre('Relance n°'.(count($relanceIdPret)+1));

        $produits = $idPrets->getProduit()->toArray();
        $nomProduits = array_map(function($produit) {
            return $produit->getNom();
        }, $produits);
        $produitListe = implode(', ', $nomProduits);

        $titre = 'Relance d\'emprunt';

        $datePret = date_format($idPrets->getDatePret(), 'd/m/Y');
        $dateFinPrevue = date_format($idPrets->getDateFinPrevue(), 'd/m/Y');

        $params[0] = 'Bonjour' . ',' . "\n\n" .
            'Nous vous informons que votre emprunt pour le matériel ' . $produitListe . 
            ' daté du ' . $datePret . ' est arrivé à son terme depuis le ' . "\t" . $dateFinPrevue . "\n\n" .
            'Nous vous prions de bien vouloir le ramener au plus vite.' . "\n\n" .
            'Cordialement,' . "\n" .
            'Le service informatique de l\'ECIR';
        
        $params[1] = $produitListe;
        $params[2] = $datePret;
        $params[3] = $dateFinPrevue;
        $params[4] = $titre;
        

        $this->envoidemail->sendEmailCustom('leonn@ecirtp.fr', $idPrets->getUser()->getEmail(), $titre, $params);

        $relancePret->setContenu($params[0]);

        $this->entityManager->persist($relancePret);
        $this->entityManager->flush();
        return $this->redirectToRoute('app_gestion_retard_pret');
    }
}
