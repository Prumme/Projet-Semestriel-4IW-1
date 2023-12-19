<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $lastname = null;

    #[ORM\Column(length: 45)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company_name = null;

    #[ORM\Column(length: 14, nullable: true)]
    private ?string $company_siret = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $company_vat_number = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $tel = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: BillingAddress::class, cascade: ['remove'])]
    private Collection $billing_addresses;

    //----

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(?string $company_name): static
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getCompanySiret(): ?string
    {
        return $this->company_siret;
    }

    public function setCompanySiret(?string $company_siret): static
    {
        $this->company_siret = $company_siret;

        return $this;
    }

    public function getCompanyVatNumber(): ?string
    {
        return $this->company_vat_number;
    }

    public function setCompanyVatNumber(?string $company_vat_number): static
    {
        $this->company_vat_number = $company_vat_number;

        return $this;
    }


    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;
        return $this;
    }

    public function getReferenceCompany(): Company
    {
        return $this->company;
    }

    public function setReferenceCompany(Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    public function getBillingAddresses(): Collection
    {
        return $this->billing_addresses;
    }

    public function addBillingAddress(BillingAddress $billing_address): static
    {
        if (!$this->billing_addresses->contains($billing_address)) {
            $this->billing_addresses->add($billing_address);
            $billing_address->setCustomer($this);
        }
        return $this;
    }

    public function removeBillingAddress(BillingAddress $billing_address): static
    {
        if (!empty($this->billing_addresses)) {
            if ($this->billing_addresses->contains($billing_address)) {
                $this->billing_addresses->removeElement($billing_address);
                if ($billing_address->getCustomer() === $this) {
                    $billing_address->setCustomer(null);
                }
            }
        }
        return $this;
    }
}
