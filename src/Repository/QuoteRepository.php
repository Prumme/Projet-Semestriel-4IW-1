<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Quote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 *
 * @method Quote|null find($id, $lockMode = null, $lockVersion = null)
 * @method Quote|null findOneBy(array $criteria, array $orderBy = null)
 * @method Quote[]    findAll()
 * @method Quote[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }


    public function findAllWithinCompany(Company $company)
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->join('q.owner',"o")
            ->where("o.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }
}
