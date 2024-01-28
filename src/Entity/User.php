<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 45)]
    private ?string $firstname = null;

    #[ORM\Column(length: 45)]
    private ?string $lastname = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    #[ORM\Column]
    private ?bool $activate = false;

    #[ORM\Column]
    private ?bool $resetPassword = false;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * Returns the id of the user.
     * 
     * @return int|null The id of the user.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Returns the email of the user.
     * 
     * @return string|null The email of the user.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Sets the email of the user.
     * 
     * @param string $email The email of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Sets the roles of the user.
     * 
     * @param array $roles The roles of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Checks if the user has the given role.
     * 
     * @param string $role The role to check.
     * 
     * @return bool True if the user has the given role, false otherwise.
     */
    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Sets the password of the user.
     * 
     * @param string $password The password of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Returns the firstname of the user.
     * 
     * @return string|null The firstname of the user.
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Sets the firstname of the user.
     * 
     * @param string $firstname The firstname of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Returns the lastname of the user.
     * 
     * @return string|null The lastname of the user.
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Sets the lastname of the user.
     * 
     * @param string $lastname The lastname of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Returns the company of the user.
     * 
     * @return Company The company of the user.
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * Sets the company of the user.
     * 
     * @param Company $company The company of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setCompany(Company $company): static
    {
        $this->company = $company;
        return $this;
    }

    /**
     * Returns the firstname + lastname of the user.
     * 
     * @return string The identity of the user.
     */
    public function getIdentity(): string
    {
        return $this->getFirstname() . ' ' . $this->getLastname();
    }
    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Collection<int, Product> $products
     * 
     * @return User
     */
    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUserId($this);
        }

        return $this;
    }

    /**
     * @param Collection<int, Product> $products
     * 
     * @return User
     */
    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getUserId() === $this) {
                $product->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * Check if the user has an active account (email verified).
     * 
     * @return bool|null The activate of the user.
     */
    public function isActivate(): ?bool
    {
        return $this->activate;
    }

    /**
     * Sets the active of the user.
     * 
     * @param bool $activate The activate of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setActivate(bool $activate): static
    {
        $this->activate = $activate;

        return $this;
    }

    /**
     * Check if the user ask for a password reset.
     * 
     * @return bool|null The activate of the user.
     */
    public function isResetPassword(): ?bool
    {
        return $this->resetPassword;
    }

    /**
     * Sets the resetPassword of the user.
     * 
     * @param bool $resetPassword The resetPassword of the user.
     * 
     * @return User The current instance of the user.
     */
    public function setResetPassword(bool $resetPassword): static
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }
}
