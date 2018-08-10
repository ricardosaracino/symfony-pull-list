<?php

namespace App\Repository;

use App\Entity\UserPurchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserPurchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserPurchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserPurchase[]    findAll()
 * @method UserPurchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserPurchaseRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserPurchase::class);
    }

//    /**
//     * @return UserPurchase[] Returns an array of UserPurchase objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserPurchase
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
