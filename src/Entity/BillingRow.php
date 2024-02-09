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

    #[ORM\ManyToOne(inversedBy: 'billingRows', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Quote $quote_id = null;


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
    public function getTotalWithVAT(){
        return $this->getTotal() * (1 + $this->getVat() / 100);
    }
}
