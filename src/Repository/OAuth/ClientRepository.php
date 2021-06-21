<?php

namespace App\Repository\OAuth;

use App\Entity\OAuth\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Client|null find($id, $lockMode = null, $lockVersion = null)
 * @method Client|null findOneBy(array $criteria, array $orderBy = null)
 * @method Client[]    findAll()
 * @method Client[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @param string $identifier
     * @return Client|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findOneByIdentifier(string $identifier): ?Client
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.identifier = :identifier')
            ->andWhere('c.isActif = :actif')
            ->setParameter('identifier', $identifier)
            ->setParameter('actif', true)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /*
    public function findOneBySomeField($value): ?Client
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
