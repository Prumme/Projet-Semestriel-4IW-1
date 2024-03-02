<?php

namespace App\Repository;

use App\Entity\Company;
use App\Entity\Quote;
use App\Entity\BillingRow;
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


    public function findAllWithingCompany(Company $company)
    {
        return $this->createQueryBuilder('q')
            ->select('q')
            ->join('q.owner',"o")
            ->where("o.company = :company")
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
    }

    public function monthlyQuotesCount($company): array
    {
        $queryBuilder = $this->createQueryBuilder('q')
            ->select('COUNT(q.id) as quotes_count')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->andWhere('q.emited_at BETWEEN :start AND :end');

        $result['thisMonth'] = $queryBuilder
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of this month'))
            ->setParameter('end', new \DateTime('last day of this month'))
            ->getSingleScalarResult();

        $result['lastMonth'] = $queryBuilder
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

    public function getAvgQuotePrice($company): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id AS quote_id', 'SUM(br.unit * br.quantity) AS total_amount')
            ->leftJoin('q.billingRows', 'br')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->andWhere('q.signature IS NOT NULL')
            ->andWhere('q.emited_at BETWEEN :start AND :end')
            ->groupBy('q.id');

        $result = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of this month'))
            ->setParameter('end', new \DateTime('last day of this month'))
            ->getResult();

        $avgQuotePrice = 0;
        $count = 0;
        foreach ($result as $quote) {
            if($quote['total_amount'] != null) {
                $avgQuotePrice += $quote['total_amount'];     
            }
            $count++;
        }
        if($count == 0) {
            $data['avg_quote_price'] = 0;
        }
        else {
            $data['avg_quote_price'] = number_format($avgQuotePrice / $count, 2);
        }

        $prevResult = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->setParameter('start', new \DateTime('first day of last month'))
            ->setParameter('end', new \DateTime('last day of last month'))
            ->getResult();
        
        $prevAvgQuotePrice = 0;
        $prevCount = 0;
        foreach ($prevResult as $quote) {
            if($quote['total_amount'] != null) {
                $prevAvgQuotePrice += $quote['total_amount'];     
            }
            $prevCount++;
        }

        if($prevCount == 0) {
            $data['prev_avg_quote_price'] = 0;
        }
        else {
            $data['prev_avg_quote_price'] = $prevAvgQuotePrice / $prevCount;
        }

        if($data['prev_avg_quote_price'] == 0) {
            $data['evolution_rate_percentage'] = 100;
        }
        else{
            $data['evolution_rate_percentage'] = number_format(($data['avg_quote_price'] - $data['prev_avg_quote_price']) / abs($data['prev_avg_quote_price']) * 100, 2);
        }
        return $data;
        
    }

    public function topFiveQuotes($company): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('q.id AS quote_id', 'SUM(br.unit * br.quantity) AS total_amount', 'c.company_name AS customer_name', 'o.firstname AS owner_first_name', 'UPPER(o.lastname) AS owner_last_name', 'q.emited_at AS date')
            ->leftJoin('q.billingRows', 'br')
            ->join('q.owner', 'o')
            ->join('q.customer', 'c')
            ->where('o.company = :company')
            ->andWhere('q.signature IS NOT NULL')
            ->groupBy('q.id', 'c.company_name', 'o.firstname', 'o.lastname')
            ->orderBy('total_amount', 'DESC')
            ->setMaxResults(5);

        $result = $qb
            ->getQuery()
            ->setParameter('company', $company)
            ->getResult();
        
        foreach($result as $key => $value) {
            $result[$key]['date'] = $value['date']->format('Y-m-d');
        }
        
        return $result;
    }

    public function getSignedQuoteValuesByDay($company): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select("q.emited_at AS day", 'SUM(br.unit * br.quantity) AS total_amount')
            ->join('q.billingRows', 'br')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->andWhere('q.signature IS NOT NULL')
            ->andWhere('q.emited_at BETWEEN :start AND :end')
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

    public function getAllQuoteValuesByDay($company): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select("q.emited_at AS day", 'SUM(br.unit * br.quantity) AS total_amount')
            ->join('q.billingRows', 'br')
            ->join('q.owner', 'o')
            ->where('o.company = :company')
            ->andWhere('q.emited_at BETWEEN :start AND :end')
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
