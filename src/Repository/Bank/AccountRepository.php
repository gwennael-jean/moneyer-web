<?php

namespace App\Repository\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[]    findAll()
 * @method Account[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

     /**
      * @return Account[] Returns an array of Account objects
      */
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.accountShares', 's')
            ->andWhere('a.owner = :user OR s.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Account
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
