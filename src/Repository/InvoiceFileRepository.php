<?php

namespace App\Repository;

use App\Entity\InvoiceFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvoiceFile>
 *
 * @method InvoiceFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceFile[]    findAll()
 * @method InvoiceFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvoiceFile::class);
    }

//    /**
//     * @return InvoiceFile[] Returns an array of InvoiceFile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InvoiceFile
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
