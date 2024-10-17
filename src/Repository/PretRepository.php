<?php

namespace App\Repository;

use App\Entity\Pret;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @extends ServiceEntityRepository<Pret>
 *
 * @method Pret|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pret|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pret[]    findAll()
 * @method Pret[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PretRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pret::class);
    }

    public function findByActive(): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateFin is null OR p.dateFin > :dateActuelle')    
            ->setParameter('dateActuelle', new \DateTime())
            ->getQuery()
            ->getResult();
    }
    
    public function pretEnRetard(): array
    {
        $dateActuelle = new \DateTime();
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateFinPrevue < :dateActuelle')
            ->andWhere('p.dateFin is null')
            ->setParameter('dateActuelle', $dateActuelle)
            ->getQuery()
            ->getResult();
    }

    public function pretEnRetardOrderByDateFinPrevue(): array
    {
        $dateActuelle = new \DateTime();
        return $this->createQueryBuilder('p')
            ->andWhere('p.dateFinPrevue < :dateActuelle')
            ->setParameter('dateActuelle', $dateActuelle)
            ->orderBy('p.dateFinPrevue', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByLate(): array
    {
        $dateActuelle = new \DateTime();

        return $this->createQueryBuilder('p')
            ->where('p.dateFinPrevue < :dateActuelle')
            ->andWhere('p.dateFin IS NULL')
            ->setParameter('dateActuelle', $dateActuelle)
            ->getQuery()
            ->getResult();
    }

    public function findByYear(): array
    {
        $premierJourDeAnnee = new \DateTime('first day of January this year midnight');
        $dernierJourDeAnnee = new \DateTime('last day of December this year 23:59:59');

        return $this->createQueryBuilder('p')
        ->andWhere('p.datePret between :debutAnnee and :finAnnee')
        ->setParameter('debutAnnee', $premierJourDeAnnee)
        ->setParameter('finAnnee', $dernierJourDeAnnee)
        ->getQuery()
        ->getResult()
        ;
    }

    public function findByLastYear(): array
    {
        $premierJourDeAnneePassee = new \DateTime('first day of January previous year midnight');
        $dernierJourDeAnneePassee = new \DateTime('last day of December previous year 23:59:59');

        return $this->createQueryBuilder('p')
        ->andWhere('p.datePret between :debutAnneePassee and :finAnneePassee')
        ->setParameter('debutAnneePassee', $premierJourDeAnneePassee)
        ->setParameter('finAnneePassee', $dernierJourDeAnneePassee)
        ->getQuery()
        ->getResult()
        ;
    }

    public function findByThisMonth(): array
    {
        $premierJourDuMois = new \DateTime('first day of this month midnight');
        $dernierJourDuMois = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('p')
            ->andWhere('p.datePret between :debutMois and :finMois')
            ->setParameter('debutMois', $premierJourDuMois)
            ->setParameter('finMois', $dernierJourDuMois)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByPreviousYear(): array
    {
        $premierJourDuMoisAnneeDerniere = (new \DateTime('first day of this month midnight'))->modify('-1 year');
        $dernierJourDuMoisAnneeDerniere = (new \DateTime('last day of this month 23:59:59'))->modify('-1 year');
        return $this->createQueryBuilder('p')
            ->andWhere('p.datePret between :debutMois and :finMois')
            ->setParameter('debutMois', $premierJourDuMoisAnneeDerniere)
            ->setParameter('finMois', $dernierJourDuMoisAnneeDerniere)
            ->getQuery()
            ->getResult()
        ;
    }
}
