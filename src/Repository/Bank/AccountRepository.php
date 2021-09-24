<?php

namespace App\Repository\Bank;

use App\Entity\Bank\Account;
use App\Entity\User;
use App\Util\Form\FormFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
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
    public function findByUser(User $user, ?FormFilter $formFilter = null)
    {
        $queryBuilder = $this->createQueryBuilder('a');

        $queryBuilder
            ->leftJoin('a.accountShares', 's')
            ->andWhere('a.createdBy = :user OR a.owner = :user OR s.user = :user')
            ->setParameter('user', $user)
            ->orderBy("IF(a.owner = :user, 1, 0)", Criteria::DESC)
            ->addOrderBy("a.id");

        if (null !== $formFilter && $formFilter->hasCriteria()) {
            $queryBuilder->addCriteria($formFilter->getCriteria());
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
}
