<?php

namespace App\Repository;

use App\Entity\RelancePret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RelancePret>
 */
class RelancePretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RelancePret::class);
    }

    //    /**
    //     * @return RelancePret[] Returns an array of RelancePret objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findRelancePrets($prets)
    {
        $qb = $this->createQueryBuilder('r')
            ->andWhere('r.pret IN (:prets)')
            ->setParameter('prets', $prets)
            ->getQuery()
            ->getResult();
        return $qb;
    }

    //    public function findOneBySomeField($value): ?RelancePret
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
