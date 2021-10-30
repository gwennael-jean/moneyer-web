<?php

namespace App\Repository\Bank;

use App\Entity\Bank\ResourceGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResourceGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResourceGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResourceGroup[]    findAll()
 * @method ResourceGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResourceGroup::class);
    }

    public function findByAccountsAndDate(ArrayCollection $accounts, \DateTime $date): array
    {
        $queryBuilder = $this->createQueryBuilder('rg');

        return $queryBuilder
            ->join('rg.account', 'a')
            ->andWhere('a IN (:accounts)')
            ->setParameter('accounts', $accounts)
            ->getQuery()
            ->getResult()
            ;
    }

    /*
    public function findOneBySomeField($value): ?ResourceGroup
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
