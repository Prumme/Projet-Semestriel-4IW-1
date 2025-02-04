<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Security\AuthentificableRoles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'],message: 'There is already an account with this email')]
#[ORM\Table(name: '`user`')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    use Traits\TimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.'), Assert\NotBlank(message: 'Please enter an email')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(nullable: true)]
    #[Assert\Length(min: 8, minMessage: 'Your password should be at least 8 characters')]
    private ?string $password = null;

    #[ORM\Column(length: 45)]
    #[Assert\Length(min: 2, minMessage: 'Your firstname should be at least 2 characters'), Assert\NotBlank(message: 'Please enter please enter a firstname')]
    private ?string $firstname = null;

    #[ORM\Column(length: 45)]
    #[Assert\Length(min: 2, minMessage: 'Your lastname should be at least 2 characters'), Assert\NotBlank(message: 'Please enter please enter a lastname')]
    private ?string $lastname = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    #[ORM\Column]
    private ?bool $activate = false;

    #[ORM\Column]
    private ?bool $resetPassword = false;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Product::class, cascade: ['persist', 'remove'])]
    private Collection $products;

    #[ORM\OneToMany(targetEntity: Quote::class, mappedBy: 'owner')]
    private Collection $quotes;


    #[Vich\UploadableField(mapping: 'profile_picture', fileNameProperty: 'profilePictureName')]
    private ?File $profilePictureFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $profilePictureName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $theme = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole($role): bool
    {
        return in_array($role, $this->getRoles());
    }

    public function hasUpperRole(User $comparedUser) : bool
    {
        $roles = $this->getRoles();
        $comparedRoles = $comparedUser->getRoles();
        $rolesIndex = array_map(fn($role) => array_search($role, AuthentificableRoles::hierarchy()), $roles);
        $comparedRolesIndex = array_map(fn($role) => array_search($role, AuthentificableRoles::hierarchy()), $comparedRoles);
        $minRoleIndex = min($rolesIndex);
        $comparedMinRoleIndex = min($comparedRolesIndex);
        return $minRoleIndex < $comparedMinRoleIndex;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
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

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): static
    {
        $this->company = $company;
        return $this;
    }

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

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setUserId($this);
        }

        return $this;
    }

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


    public function isActivate(): ?bool
    {
        return $this->activate;
    }

    public function setActivate(bool $activate): static
    {
        $this->activate = $activate;

        return $this;
    }

    public function isResetPassword(): ?bool
    {
        return $this->resetPassword;
    }

    public function setResetPassword(bool $resetPassword): static
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function getprofilePictureName(): ?string
    {
        return $this->profilePictureName;
    }

    public function setProfilePictureName(?string $profilePictureName): void
    {
        $this->profilePictureName = $profilePictureName;

    }

    public function setProfilePictureFile(?File $profilePictureFile = null): void
    {
        $this->profilePictureFile = $profilePictureFile;

        if (null !== $profilePictureFile) {

            $this->updatedAt = new \DateTime();
        }

    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }

    public function getQuotes() : Collection
    {
        return $this->quotes;
    }

}
