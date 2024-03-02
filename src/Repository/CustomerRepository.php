<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findAllWithingCompany(Company $company)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }

    public function countAllWithingCompany(Company $company)
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id) as customers_count')
            ->where('c.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
