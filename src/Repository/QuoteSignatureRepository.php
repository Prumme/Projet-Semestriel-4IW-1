<?php

namespace App\Repository;

use App\Entity\QuoteSignature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuoteSignature>
 *
 * @method QuoteSignature|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteSignature|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuoteSignature[]    findAll()
 * @method QuoteSignature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteSignatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteSignature::class);
    }

//    /**
//     * @return QuoteSignature[] Returns an array of QuoteSignature objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('q.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?QuoteSignature
//    {
//        return $this->createQueryBuilder('q')
//            ->andWhere('q.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
