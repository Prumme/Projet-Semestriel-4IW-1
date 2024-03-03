<?php

namespace App\Entity;

use App\Repository\QuoteSignatureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: QuoteSignatureRepository::class)]
class QuoteSignature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $dataBase64URI = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $signedAt = null;

    #[Assert\NotBlank,Assert\Length(
        min: 3,
        max: 255,
        minMessage: "The name must be at least {{ limit }} characters long",
        maxMessage: "The name cannot be longer than {{ limit }} characters"
    )]
    #[ORM\Column(length: 255)]
    private ?string $signedBy = null;

    #[Assert\EqualTo(true, message: "You must mark this quote as agreed to sign it")]
    private bool $hasBeenAgreed = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->dataBase64URI;
    }

    public function setDataBase64URI(string $dataBase64URI): static
    {
        $this->dataBase64URI = $dataBase64URI;

        return $this;
    }

    public function getSignedAt(): ?\DateTimeImmutable
    {
        return $this->signedAt;
    }

    public function setSignedAt(\DateTimeImmutable $signedAt): static
    {
        $this->signedAt = $signedAt;

        return $this;
    }

    public function getSignedBy(): ?string
    {
        return $this->signedBy;
    }

    public function setSignedBy(string $signedBy): static
    {
        $this->signedBy = $signedBy;

        return $this;
    }

    public function setHasBeenAgreed(bool $hasBeenAgreed): static
    {
        $this->hasBeenAgreed = $hasBeenAgreed;
        return $this;
    }
}
