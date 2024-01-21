<?php

namespace App\Service;

use App\Entity\BillingRow;
use App\Entity\Quote;
use Doctrine\ORM\EntityManagerInterface;

class QuoteService
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function syncBillingRows(Quote $quote): void
    {
        $current_billing_rows =  $this->entityManager->getRepository(BillingRow::class)->findBy(['quote_id' => $quote->getId()]);
        foreach ($quote->getBillingRows() as $billingRow) {
            if(!$billingRow->getQuoteId())
                $billingRow->setQuoteId($quote);
        }
        foreach ($current_billing_rows as $current_billing_row) {
            if (!$quote->getBillingRows()->contains($current_billing_row)) {
                $this->entityManager->remove($current_billing_row);
            }
        }
        $this->entityManager->flush();
    }
}