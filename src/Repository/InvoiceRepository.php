<?php

namespace App\Repository;

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

    public function findAllWithinCompany(Company $company,$quote = null)
    {
        $query =  $this->createQueryBuilder('q')
            ->select('q')
            ->join("q.quote","qu")
            ->join('qu.owner',"o")
            ->where("o.company = :company")
            ->setParameter('company', $company);
        if($quote){
            $query->andWhere("q.quote = :quote")
                ->setParameter('quote', $quote);
        }
        return $query->getQuery()->getResult();
    }
}
