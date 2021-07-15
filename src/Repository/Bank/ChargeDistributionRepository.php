<?php

namespace App\Repository\Bank;

use App\Entity\Bank\ChargeDistribution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChargeDistribution|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChargeDistribution|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChargeDistribution[]    findAll()
 * @method ChargeDistribution[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargeDistributionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChargeDistribution::class);
    }

    // /**
    //  * @return ChargeDistribution[] Returns an array of ChargeDistribution objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ChargeDistribution
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
