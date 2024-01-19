<?php

namespace App\Repository;

use App\Entity\BillingR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BillingR>
 *
 * @method BillingR|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillingR|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillingR[]    findAll()
 * @method BillingR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingRRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingR::class);
    }

//    /**
//     * @return BillingR[] Returns an array of BillingR objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BillingR
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
