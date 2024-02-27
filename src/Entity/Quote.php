<?php

namespace App\Entity;

use App\Repository\QuoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: QuoteRepository::class)]
#[Assert\Callback([Quote::class, 'validateBillingRowsNotEmpty'])]
class Quote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\LessThan(propertyPath: 'expired_at', message: 'The date of issue must be less than the expiration date.')]
    private ?\DateTimeInterface $emited_at = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThan(propertyPath: 'emited_at', message: 'The expiration date must be greater than the date of issue.')]
    private ?\DateTimeInterface $expired_at = null;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: Invoice::class)]
    private Collection $invoices;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'quotes', cascade: ['remove'])]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[Assert\NotNull(message: 'The customer is required.')]
    private ?Customer $customer = null;
    #[ORM\OneToMany(mappedBy: 'quote_id', targetEntity: BillingRow::class,  cascade: ['persist'], orphanRemoval: true)]
    #[Assert\Valid]
    private Collection $billingRows;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotNull(message: 'The billing address is required.')]
    private ?BillingAddress $billingAddress = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?QuoteSignature $signature = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'quote', targetEntity: QuoteDiscount::class, cascade: ['persist'],orphanRemoval: true)]
    private Collection $discounts;

    public function __construct()
    {
        $this->has_been_signed = false;
        $this->billingRows = new ArrayCollection();
        $this->invoices = new ArrayCollection();
        $this->discounts = new ArrayCollection();
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
        return $this->signature !== null;
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
            if ($billingRow->getQuoteId() === $this) {
                $billingRow->setQuoteId(null);
            }
        }
        return $this;
    }

    public  function getTotal(){
        $total = 0;
        foreach ($this->getBillingRows() as $billingRow) {
            $total += $billingRow->getTotalWithDiscount();
        }
        return $total;
    }

    public function getTotalRowScoped(){
        $total = 0;
        foreach ($this->getBillingRows() as $billingRow) {
            $total += $billingRow->getTotalWithDiscountRowScoped();
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

    public function getNumber():string
    {
        return str_pad($this->getId(), 5, "0", STR_PAD_LEFT);
    }

    public function getSignature(): ?QuoteSignature
    {
        return $this->signature;
    }
    public function getIsSigned(): bool
    {
        return $this->signature !== null;
    }

    public function setSignature(?QuoteSignature $signature): static
    {
        $this->signature = $signature;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public static function validateBillingRowsNotEmpty(Quote $quote, ExecutionContextInterface $context): void
    {
        if ($quote->getBillingRows()->isEmpty()) {
            $context->buildViolation('The quote must have at least one billing row.')
                ->atPath('billingRows')
                ->addViolation();
        }
    }

    public function addDiscount(QuoteDiscount $discount): static
    {
        if (!$this->discounts->contains($discount)) {
            $this->discounts->add($discount);
            $discount->setQuote($this);
        }

        return $this;
    }

    public function removeDiscount(QuoteDiscount $discount): static
    {
        if ($this->discounts->removeElement($discount)) {
            if ($discount->getQuote() === $this) {
                $discount->setQuote(null);
            }
        }
        return $this;
    }

    public function getDiscounts(): Collection
    {
        return $this->discounts;
    }

    public function getDiscountsDetails() : array
    {
        $total = $this->getTotalRowScoped();
        $discounts = $this->getDiscounts();
        $discountsDetails = [];
        foreach ($discounts as $discount) {
            $totalAtTheMoment = $total;
            if($discount->getDiscount()->getType() == Discount::TYPE_PERCENTAGE){
                $totalAtTheMoment -= $total * $discount->getDiscount()->getValue() / 100;
            }else{
                $totalAtTheMoment -= $discount->getDiscount()->getValue();
            }
            $discountsDetails[] = [
                'label' => $discount->getDiscount()->getFormated(),
                'total' => $totalAtTheMoment,
            ];
        }
        return $discountsDetails;
    }

    public function hasDiscounts(): bool
    {
        return !$this->discounts->isEmpty();
    }

}
