<?php

namespace App\Repository;

use App\Entity\Creator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Creator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creator[]    findAll()
 * @method Creator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreatorRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Creator::class);
    }

//    /**
//     * @return Creator[] Returns an array of Creator objects
//     */
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
    public function findOneBySomeField($value): ?Creator
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
