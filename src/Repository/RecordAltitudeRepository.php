<?php

namespace App\Repository;

use App\Entity\RecordAltitude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecordAltitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecordAltitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecordAltitude[]    findAll()
 * @method RecordAltitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordAltitudeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecordAltitude::class);
    }

    // /**
    //  * @return RecordAltitude[] Returns an array of RecordAltitude objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecordAltitude
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
