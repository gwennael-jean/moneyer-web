<?php

namespace App\Repository\Bank;

use App\DBAL\Types\MonthType;
use App\Entity\Bank\Charge;
use App\Entity\User;
use App\Util\Form\FormFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Charge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Charge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Charge[]    findAll()
 * @method Charge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChargeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Charge::class);
    }

    /**
     * @return Charge[] Returns an array of Charge objects
     */
    public function findByDateAndUser(\DateTimeInterface $date, User $user, ?FormFilter $formFilter = null)
    {
        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->join('c.account', 'a')
            ->leftJoin('a.accountShares', 's')
            ->andWhere('a.createdBy = :user OR a.owner = :user OR s.user = :user')
            ->andWhere('c.month = :month')
            ->setParameter('user', $user)
            ->setParameter('month', $date, MonthType::NAME);

        if (null !== $formFilter && $formFilter->hasCriteria()) {
            $queryBuilder->addCriteria($formFilter->getCriteria());
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Charge[] Returns an array of Charge objects
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
    public function findOneBySomeField($value): ?Charge
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
