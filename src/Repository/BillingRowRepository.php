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

    public function bestFiveProduct($company): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.product as label, SUM(b.quantity) as data')
            ->join('b.quote_id', 'q')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->setParameter('company', $company)
            ->groupBy('b.product')
            ->orderBy('data', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function productSelled($company): array
    {
        return $this->createQueryBuilder('b')
            ->select('b.product as label, SUM(b.quantity) as data')
            ->join('b.quote_id', 'q')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->setParameter('company', $company)
            ->groupBy('b.product')
            ->getQuery()
            ->getResult();
    }

    public function totalEarned($company): array
    {
        return $this->createQueryBuilder('b')
            ->select('SUM(b.price) as data')
            ->join('b.quote_id', 'q')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
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
