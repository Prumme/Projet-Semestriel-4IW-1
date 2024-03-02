<?php

namespace App\Entity;

use App\Service\InvoiceService;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{

    const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    const STATUS_PAID = 'paid';
    const STATUS_UNPAID = 'unpaid';
    const STATUS_CANCELLED = 'cancelled';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 11)]
    private ?string $number = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $emitted_at = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $expired_at = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    private ?Quote $quote = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->number;
    }

    public function getCustomer() : ?Customer
    {
        return $this->quote->getCustomer();
    }

    public function setInvoiceNumber(?InvoiceService $invoiceService = null, ?Quote $quote = null): static
    {
        if(!$quote || !$invoiceService){
            $this->number = $this->number;
            return $this;
        }

        $currentYear = date('Y');

        $lastInvoiceNumber = $invoiceService->getLastInvoiceNumber();

        if ($lastInvoiceNumber !== null) {
            $uniqueNumber = $lastInvoiceNumber + 1;
        } else {
            $uniqueNumber = 1;
        }

        $invoiceNumber = "n°{$currentYear}-" . str_pad($uniqueNumber, 4, '0', STR_PAD_LEFT);

        $this->number = $invoiceNumber;

        return $this;
    }
    
    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEmittedAt(): ?\DateTimeInterface
    {
        return $this->emitted_at;
    }

    public function setEmittedAt(?\DateTimeInterface $emitted_at): static
    {
        $this->emitted_at = $emitted_at;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expired_at;
    }

    public function setExpiredAt(?\DateTimeInterface $expired_at): static
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }
    public function getQuoteNumber(): ?string
    {
        return $this->quote->getFormattedNumber();
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }
}
