<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    const TYPE_PERCENTAGE = 1;
    const TYPE_FIXED = 2;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Choice(choices: [self::TYPE_PERCENTAGE, self::TYPE_FIXED])]
    private ?int $type = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank, Assert\PositiveOrZero]
    private ?string $value = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {

        if(!in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_FIXED])) {
            throw new \InvalidArgumentException('Invalid type');
        }

        $this->type = $type;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue($value): static
    {
        $this->value = $value;
        return $this;
    }

    public function getFormated(): string
    {
        return $this->type === self::TYPE_PERCENTAGE ? $this->value . '%' : $this->value . '€';
    }
}
