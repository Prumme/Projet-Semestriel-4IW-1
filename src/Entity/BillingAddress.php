<?php

namespace App\Entity;

use App\Repository\BillingAddressRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillingAddressRepository::class)]
class BillingAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column]
    private ?int $zip_code = null;

    #[ORM\Column(length: 2)]
    private ?string $country_code = null;

    #[ORM\Column(length: 255)]
    private ?string $address_line_1 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address_line_2 = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private Customer $customer;

    private EntityManager $manager;
    public function __construct(EntityManager $entityManager = null)
    {
        $this->manager = $entityManager;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getZipCode(): ?int
    {
        return $this->zip_code;
    }

    public function setZipCode(int $zip_code): static
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    public function setCountryCode(string $country_code): static
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->address_line_1;
    }

    public function setAddressLine1(string $address_line_1): static
    {
        $this->address_line_1 = $address_line_1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->address_line_2;
    }

    public function setAddressLine2(?string $address_line_2): static
    {
        $this->address_line_2 = $address_line_2;

        return $this;
    }

    public function setCustomer(?Customer $customer = null) : void
    {
        if(!isset($customer)) {
            try{
                $this->manager->remove($this);
                $this->manager->flush();

            }catch(\Exception $e){
                throw new \Exception("Error while removing BillingAddress");
            }

        }
        else $this->customer = $customer;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getFullAddress(){
        return $this->address_line_1 . " " . $this->address_line_2 . " " . $this->zip_code . " " . $this->city . " " . $this->country_code;
    }
}
