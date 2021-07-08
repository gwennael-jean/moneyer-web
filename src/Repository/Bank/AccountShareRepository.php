<?php

namespace App\Repository\Bank;

use App\Entity\Bank\AccountShare;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccountShare|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccountShare|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccountShare[]    findAll()
 * @method AccountShare[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountShareRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountShare::class);
    }

    // /**
    //  * @return AccountShare[] Returns an array of AccountShare objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AccountShare
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
