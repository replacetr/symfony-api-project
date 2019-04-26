<?php

namespace App\Repository;

use App\Entity\Backgroud;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Backgroud|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backgroud|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backgroud[]    findAll()
 * @method Backgroud[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackgroudRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Backgroud::class);
    }

    // /**
    //  * @return Backgroud[] Returns an array of Backgroud objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Backgroud
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
