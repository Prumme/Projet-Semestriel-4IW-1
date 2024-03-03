<?php

namespace App\Repository;

use App\Entity\QuoteDiscount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuoteDiscount>
 *
 * @method QuoteDiscount|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuoteDiscount|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuoteDiscount[]    findAll()
 * @method QuoteDiscount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteDiscountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuoteDiscount::class);
    }
}
