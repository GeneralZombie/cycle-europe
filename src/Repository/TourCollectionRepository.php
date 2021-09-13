<?php

namespace App\Repository;

use App\Entity\TourCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TourCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method TourCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method TourCollection[]    findAll()
 * @method TourCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TourCollection::class);
    }

    // /**
    //  * @return TourCollection[] Returns an array of Tour objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TourCollection
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
