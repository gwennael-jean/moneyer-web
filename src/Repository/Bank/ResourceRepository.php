<?php

namespace App\Repository\Bank;

use App\DBAL\Types\MonthType;
use App\Entity\Bank\Account;
use App\Entity\Bank\Resource;
use App\Entity\User;
use App\Util\Form\FormFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function findByDateAndUser(\DateTimeInterface $date, User $user, ?FormFilter $formFilter = null): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        $queryBuilder
            ->join('r.account', 'a')
            ->leftJoin('a.accountShares', 's')
            ->andWhere('a.createdBy = :user OR a.owner = :user OR s.user = :user')
            ->andWhere('r.month = :month')
            ->setParameter('user', $user)
            ->setParameter('month', $date, MonthType::NAME);

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
    public function findByOwner(User $user): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->join('r.account', 'a')
            ->andWhere('a.owner = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findByAccountsAndDate(ArrayCollection $accounts, \DateTime $date): array
    {
        $queryBuilder = $this->createQueryBuilder('r');

        return $queryBuilder
            ->join('r.account', 'a')
            ->andWhere('a IN (:accounts)')
            ->andWhere('r.month = :month')
            ->setParameter('accounts', $accounts)
            ->setParameter('month', $date, MonthType::NAME)
            ->getQuery()
            ->getResult()
        ;
    }
}
