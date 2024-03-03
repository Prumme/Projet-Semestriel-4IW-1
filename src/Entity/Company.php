<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use App\Security\AuthentificableRoles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'The name of the company is required.'), Assert\Length(min: 3, max: 255, minMessage: 'The name of the company must be at least 3 characters long.')]
    private ?string $name = null;

    #[ORM\Column(length: 14, nullable: true)]
    #[Assert\NotNull(message: 'The siret of the company is required.'), Assert\Length(min: 14, max: 14, exactMessage: 'The siret number must be 14 characters long.') ]
    private ?string $siret = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\NotNull(message: 'The vat number of the company is required.'), Assert\Length(min: 15, max: 15, exactMessage: 'The vat number must be 15 characters long.')]
    private ?string $vat_number = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Customer::class)]
    private Collection $customers;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Category::class)]
    private Collection $categories;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;
    
    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?BillingAddress $address = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->vat_number;
    }

    public function setVatNumber(?string $vat_number): static
    {
        $this->vat_number = $vat_number;

        return $this;
    }

    /**
     * @param array|null $roles If null, return all users of the company
     * @return Collection|User[]
     */
    public function getUsers(?array $roles = null): Collection
    {
        if (!isset($roles))  return $this->users;
        return $this->users->filter(fn (User $user) => in_array($user->getRoles(), $roles));
    }

    public function getCustomers() : Collection
    {
        return $this->customers;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCompany($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCompany() === $this) {
                $product->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setCompany($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCompany() === $this) {
                $category->setCompany(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
    }
    
    public function getAddress(): ?BillingAddress
    {
        return $this->address;
    }

    public function setAddress(BillingAddress $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getFirstUser(): ?User
    {
        $adminUsers = $this->users->filter(fn (User $user) => $user->hasRole(AuthentificableRoles::ROLE_COMPANY_ADMIN));
        return $adminUsers->first();
    }
}
