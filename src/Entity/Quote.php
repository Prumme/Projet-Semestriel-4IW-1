<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteRepository::class)]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $emited_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $expired_at = null;

    #[ORM\Column]
    private ?bool $has_been_signed = null;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'quotes', cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Customer $customer = null;

    #[ORM\OneToMany(mappedBy: 'quote_id', targetEntity: BillingRow::class,  cascade: ['persist'])]
    private Collection $billingRows;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?BillingAddress $billingAddress = null;

    public function __construct()
    {
        $this->has_been_signed = false;
        $this->billingRows = new ArrayCollection();
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmitedAt(): ?\DateTimeInterface
    {
        return $this->emited_at;
    }

    public function setEmitedAt(\DateTimeInterface $emited_at): static
    {
        $this->emited_at = $emited_at;

        return $this;
    }

    public function getExpiredAt(): ?\DateTimeInterface
    {
        return $this->expired_at;
    }

    public function setExpiredAt(\DateTimeInterface $expired_at): static
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    public function getHasBeenSigned(): ?bool
    {
        return $this->has_been_signed;
    }

    public function setHasBeenSigned(bool $has_been_signed): static
    {
        $this->has_been_signed = $has_been_signed;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): static
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setQuote($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getQuote() === $this) {
                $invoice->setQuote(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BillingRow>
     */
    public function getBillingRows(): Collection
    {
        return $this->billingRows;
    }

    public function addBillingRow(BillingRow $billingRow): static
    {
        if (!$this->billingRows->contains($billingRow)) {
            $this->billingRows->add($billingRow);
            $billingRow->setQuoteId($this);
        }

        return $this;
    }

    public function removeBillingRow(BillingRow $billingRow): static
    {
        if ($this->billingRows->removeElement($billingRow)) {
            // set the owning side to null (unless already changed)
            if ($billingRow->getQuoteId() === $this) {
                $billingRow->setQuoteId(null);
            }
        }

        return $this;
    }

    public  function getTotal(){
        $total = 0;
        foreach ($this->getBillingRows() as $billingRow) {
            $total += $billingRow->getTotal();
        }
        return $total;
    }
    public function getTotalWithVAT(){
        $total = 0;
        foreach ($this->getBillingRows() as $billingRow) {
            $total += $billingRow->getTotalWithVAT();
        }
        return $total;
    }

    public function getBillingAddress(): ?BillingAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?BillingAddress $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

}
