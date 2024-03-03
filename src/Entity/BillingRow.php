<?php

namespace App\Entity;

use App\Repository\BillingRowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BillingRowRepository::class)]
class BillingRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: "product required.")]
    private ?string $product = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Quantity required.")]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotNull(message: "Unit price required.")]
    #[Assert\GreaterThan(value: 0, message: "Unit price must be positive value.")]
    private ?string $unit = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotNull(message: "VAT required.")]
    #[Assert\GreaterThan(value: 0, message: "VAT must be positive value.")]
    private ?string $vat = null;

    #[ORM\ManyToOne(targetEntity: Quote::class, inversedBy: 'billingRows', cascade: ['persist'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Quote $quote_id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'],orphanRemoval: true)]
    private ?Discount $discount = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->unit * $this->quantity;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat(string $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function getQuoteId(): ?Quote
    {
        return $this->quote_id;
    }

    public function setQuoteId(?Quote $quote_id): static
    {
        $this->quote_id = $quote_id;
        return $this;
    }

    public function getTotal(){
        return $this->getQuantity() * $this->getUnit();
    }

    public function getTotalWithDiscountRowScoped(){
        $discount = $this->getDiscount();
        if(!$discount) return $this->getTotal();
        if($discount->getType() == Discount::TYPE_PERCENTAGE){
            return $this->getTotal() - $this->getTotal() * $discount->getValue() / 100;
        }else{
            return $this->getTotal() - $discount->getValue();
        }
    }
    public function getTotalWithDiscount(){
        if(!$this->getHasDiscount()) return $this->getTotal();
        $discounts = $this->getAllDiscounts();
        $total = $this->getTotal();
        foreach ($discounts as $discount) {
            if($discount->getType() == Discount::TYPE_PERCENTAGE){
                $total -= $total * $discount->getValue() / 100;
            }else{
                $total -= $discount->getValue();
            }
        }
        return $total;
    }
    public function getTotalWithVAT(){
        return $this->getTotalWithDiscount() * (1 + $this->getVat() / 100);
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }
    public function getHasDiscount(){
        return count($this->getAllDiscounts()) !== 0;
    }

    public function getAllDiscounts($ignoreGlobalDiscount=false) : array
    {
        $allDiscounts = [];
        if($this->getDiscount()) $allDiscounts[] = $this->getDiscount();
        if($ignoreGlobalDiscount) return $allDiscounts;
        $quote = $this->getQuoteId();
        foreach ($quote->getDiscounts() as $discount) {
            $allDiscounts[] = $discount->getDiscount();
        }
        return $allDiscounts;
    }

    public function setDiscount(?Discount $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function setDiscountType($v){
        if($this->getDiscount() == null) $this->setDiscount(new Discount());
        $discount = $this->getDiscount();
        $discount->setType($v);
        return $this;
    }

    public function getDiscountType(){
        if($this->getDiscount() == null) return null;
        return $this->getDiscount()->getType();
    }

    public function setDiscountValue($v){
        if($this->getDiscount() == null) $this->setDiscount(new Discount());
        $discount = $this->getDiscount();
        $discount->setValue($v);
    }
    public function getDiscountValue(){
        if($this->getDiscount() == null) return null;
        return $this->getDiscount()->getValue();
    }

}
