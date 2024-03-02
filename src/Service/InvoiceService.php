<?php

namespace App\Service;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;

class InvoiceService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function getLastInvoiceNumber(): string
    {
        $lastInvoice = $this->entityManager->getRepository(Invoice::class)->getLastInvoice();

        if ($lastInvoice) {
            return (int) substr($lastInvoice->getInvoiceNumber(), -4);
        }

        return 0;
    }

}