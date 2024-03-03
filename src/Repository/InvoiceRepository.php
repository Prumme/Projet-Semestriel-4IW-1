<?php

namespace App\Repository;

use App\Data\InvoiceSearch;
use App\Entity\Company;
use App\Entity\Invoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invoice>
 *
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invoice::class);
    }

    public function getLastInvoice(): ?Invoice
    {
        return $this->createQueryBuilder('i')
            ->orderBy('i.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getQueryForCompany(Company $company){
        return $this->createQueryBuilder('i')
            ->join('i.quote', 'q')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->setParameter('company', $company);
    }
    public function filtered(Company $company, InvoiceSearch $filter){
        $query = $this->getQueryForCompany($company);

        if(!empty($filter->customer)){

            $query
                ->join('q.customer', 'c')
                ->andWhere('c.id = :customer')
                ->setParameter('customer', $filter->customer);
        }

        if(!empty($filter->quote)){
            $query
                ->andWhere('q.id = :quote')
                ->setParameter('quote', $filter->quote);
        }

        if(!empty($filter->status)){
            $query->andWhere('i.status = :status')
                ->setParameter('status', $filter->status);
        }

        return $query->getQuery()->getResult();
    }

    public function exportInvoiceData(Company $company){
        $qb = $this->createQueryBuilder('i')
            ->select('i.status', 'i.emitted_at', 'q.id as quote_id', 'SUM(br.unit * br.quantity) as total' )
            ->join('i.quote', 'q')
            ->join('q.billingRows', 'br')
            ->join('q.customer', 'c')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->andWhere("i.status LIKE 'paid'")
            ->setParameter('company', $company);
        
            $result = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->getResult();

        dd($result);

        return $result;
    }
}
