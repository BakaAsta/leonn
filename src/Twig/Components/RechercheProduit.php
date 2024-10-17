<?php

namespace App\Twig\Components;

use App\Repository\ProduitRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent('RechercheProduit')]
class RechercheProduit
{
    use DefaultActionTrait;

    #[LiveProp(writable : true)]
    public string $produitRecherche = '';

    #[LiveProp(writable : true)]
    public int $page = 1;

    #[LiveProp(writable : true)]
    public int $nbpage = 5;

    #[LiveProp(writable : true)]
    public int $paginatorPage = 1;

    #[LiveProp(writable : true)]
    public string $attributTri = 'nom';

    #[LiveProp(writable : true)]
    public string $directionTri = 'ASC';

    // construct

    public function __construct(
        private ProduitRepository $produitRepository,
        private PaginatorInterface $paginatorInterface
    )
    {
    }

    public function getData() {
        $query = $this->produitRepository->findByQuery($this->produitRecherche, $this->attributTri, $this->directionTri);
        $paginator = $this->paginatorInterface->paginate(
            $query,
            $this->page,
            $this->nbpage
        );

        // <th>Nom</th>
        //                 <th>Référence Interne</th>
        //                 <th>Référence Fabricant</th>
        //                 <th>Marque</th>
        //                 <th>Type de produit</th>
        //                 <th>Categorie</th>
        //                 <th>En Stock</th>
        //                 <th>Date de rebus</th>
        //                 <th>Commentaire</th>
        //                 <th>Dernière modification</th>
        //                 <th class="flex justify-center items-center w-full h-full">Actions</th>

        // met tout les th dans la liste ci-dessous
        $liste_th = [
            'Nom',
            'Référence Interne',
            'Référence Fabricant',
            'Marque',
            'Quantité',
            'Type de produit',
            'Categorie',
            'En Stock',
            'Date de rebus',
            'Commentaire',
            'Dernière modification',
            'Actions'
        ];

        $listeAttribut = [
            'nom',
            'refInterne',
            'refFabricant',
            'commentaire',
            'rebus',
            'dateRebus',
            'updatedAt',
            'typeProduit',
            'categories',
            'marque',
        ];
        // dd()
        
        $pageCount = ceil($paginator->getTotalItemCount() / $paginator->getItemNumberPerPage());
        $pageCurrent = $paginator->getCurrentPageNumber();

        if ($pageCurrent > 1) {
            $previous = $pageCurrent - 1;
        }

        if ($pageCurrent < ($pageCount)) {
            $next = $pageCurrent + 1;
        }

        if ($pageCurrent > 1) {
            $first = 1;
        }

        if ($pageCurrent < $pageCount) {
            $last = $pageCount;
        }

        if ($pageCount <= 3) {
            $pageInRange = range(1, $pageCount);
        } else {
            if ($pageCurrent == 1) {
                $pageInRange = [1, 2, 3];
            } elseif ($pageCurrent == $pageCount) {
                $pageInRange = [$pageCount - 2, $pageCount - 1, $pageCount];
            } else {
                // Eviter d'avoir des doublons
                $pageInRange = array_unique([$pageCurrent - 1, $pageCurrent, $pageCurrent + 1]);
            }
        }

        $result = [
            'produits' => $paginator,
            'pageCount' => $pageCount,
            'current' => $pageCurrent,
            'pagesInRange' => $pageInRange,
        ];
        
        if (isset($previous)) {
            $result['previous'] = $previous;
        }

        if (isset($next)) {
            $result['next'] = $next;
        }

        if (isset($first)) {
            $result['first'] = $first;
        }

        if (isset($last)) {
            $result['last'] = $last;
        }
        $result['liste_th'] = $liste_th;
        $result['listeAttributs'] = $listeAttribut;
        return $result;
    }
}
