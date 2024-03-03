<?php

namespace App\Entity;

use App\Repository\QuoteDiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuoteDiscountRepository::class)]
class QuoteDiscount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'discounts')]
    private ?Quote $quote = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Discount $discount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuote(): ?Quote
    {
        return $this->quote;
    }

    public function setQuote(?Quote $quote): static
    {
        $this->quote = $quote;

        return $this;
    }

    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }

    public function setDiscount(Discount $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    public function getDiscountValue(): ?string
    {
        if(!$this->getDiscount()) return null;
        return $this->discount->getValue();
    }
    public function setDiscountValue($discountValue): static
    {
        if(!$this->getDiscount()) $this->setDiscount(new Discount());
        $discount = $this->getDiscount();
        $discount->setValue($discountValue);
        return $this;
    }

    public function getDiscountType(): ?int
    {
        if(!$this->getDiscount()) return null;
        return $this->getDiscount()->getType();
    }
    public function setDiscountType(int $discountType): static
    {
        if(!$this->getDiscount()) $this->setDiscount(new Discount());
        $discount = $this->getDiscount();
        $discount->setType($discountType);
        return $this;
    }
}
