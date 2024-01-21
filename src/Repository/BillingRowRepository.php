<?php

namespace App\Repository;

use App\Entity\BillingRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BillingRow>
 *
 * @method BillingRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method BillingRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method BillingRow[]    findAll()
 * @method BillingRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BillingRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BillingRow::class);
    }

//    /**
//     * @return BillingRow[] Returns an array of BillingRow objects
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

//    public function findOneBySomeField($value): ?BillingRow
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
