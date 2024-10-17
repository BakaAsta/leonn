<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // methode findByQuery qui cherche dans le nom, la refInterne et la refFabricant
    public function findByQuery(string $query, ?string $orderBy = null, ?string $direction = null): array
    {
        $qb = $this->createQueryBuilder('p');
    
        $qb->where($qb->expr()->orX(
            $qb->expr()->like('p.nom', ':query'),
            $qb->expr()->like('p.refInterne', ':query'),
            $qb->expr()->like('p.refFabricant', ':query')
        ))
        ->setParameter('query', '%' . $query . '%');
    
        if (isset($orderBy) && isset($direction)) {
            // Vérification que la direction est bien 'ASC' ou 'DESC'
            $direction = strtoupper($direction);
            if (!in_array($direction, ['ASC', 'DESC'])) {
                throw new \InvalidArgumentException('Direction must be either "ASC" or "DESC".');
            }
    
            // Ajout de la clause ORDER BY
            $qb->addOrderBy('p.' . $orderBy, $direction);
        }
    
        return $qb->getQuery()->getResult();
    }
    

    // nextId
    public function nextId(): int
    {
        $query = $this->createQueryBuilder('c')
            ->select('MAX(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return $query + 1;
    }

    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
