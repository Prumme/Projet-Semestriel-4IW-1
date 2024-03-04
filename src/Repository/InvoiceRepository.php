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
            ->groupBy('i.id', 'q.id', 'i.status', 'i.emitted_at');

        $result = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->getResult();

        return $result;
    }

    public function monthlyNetIncome(Company $company): array
    {

        $qb = $this->createQueryBuilder('i')
            ->select('SUM(br.unit * br.quantity) as net_income')
            ->join('i.quote', 'q')
            ->join('q.owner', 'o')
            ->join('q.billingRows', 'br')
            ->where('o.company = :company')
            ->andWhere("i.status LIKE 'paid'")
            ->andWhere('i.emitted_at BETWEEN :start AND :end');

        $result['thisMonth'] = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of this month'))
            ->setParameter('end', new \DateTime('last day of this month'))
            ->getSingleScalarResult();
        
        $result['lastMonth'] = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of last month'))
            ->setParameter('end', new \DateTime('last day of last month'))
            ->getSingleScalarResult();
        
        if($result['lastMonth'] == 0) {
            $result['evolution_rate_percentage'] = 100;
        }
        else{
            $result['evolution_rate_percentage'] = number_format(($result['thisMonth'] - $result['lastMonth']) / abs($result['lastMonth']) * 100, 2);
        }

        return $result;
    }   

    public function getAllQuoteValuesByDay(Company $company): array
    {

        $qb = $this->createQueryBuilder('i')
            ->select("i.emitted_at AS day", 'SUM(br.unit * br.quantity) AS total_amount')
            ->join('i.quote', 'q')
            ->join('q.owner', 'o')
            ->join('q.billingRows', 'br')
            ->where('o.company = :company')
            ->andWhere('i.emitted_at BETWEEN :start AND :end')
            ->groupBy('day')
            ->orderBy('day');
        
        $result = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of this month'))
            ->setParameter('end', new \DateTime('last day of this month'))
            ->getResult();
        
        foreach($result as $key => $value) {
            $result[$key]['day'] = $value['day']->format('Y-m-d');
        }
        return $result;
    }
}
