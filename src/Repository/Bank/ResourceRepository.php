<?php

namespace App\Repository\Bank;

use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Entity\User;
use App\Util\Form\FormFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Resource|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resource|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resource[]    findAll()
 * @method Resource[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Resource::class);
    }

    /**
     * @return Resource[] Returns an array of Resource objects
     */
    public function findByUser(User $user, ?FormFilter $formFilter = null)
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $queryBuilder
            ->join('r.account', 'a')
            ->leftJoin('a.accountShares', 's')
            ->andWhere('a.createdBy = :user OR a.owner = :user OR s.user = :user')
            ->setParameter('user', $user);

        if (null !== $formFilter && $formFilter->hasCriteria()) {
            $queryBuilder->addCriteria($formFilter->getCriteria());
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Resource[] Returns an array of Resource objects
     */
    public function findByOwner(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->join('r.account', 'a')
            ->andWhere('a.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /*
    public function findOneBySomeField($value): ?Resource
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
