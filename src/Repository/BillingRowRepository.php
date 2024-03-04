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

    public function getBestSellers($company): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->select('b.product as product_name, SUM(b.quantity) as total_quantity')
            ->join('b.quote_id', 'q')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->groupBy('b.product')
            ->orderBy('total_quantity', 'DESC')
            ->setMaxResults(5);

        $result = $queryBuilder
            ->getQuery()
            ->setParameter('company', $company)
            ->getResult();
        return $result;
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