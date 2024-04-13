<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: "UNIQ_IDENTIFIER_EMAIL", fields: ["email"])]
#[UniqueEntity(fields: "email", message: "Un utilisateur s'est déjà inscrit avec cette adresse email")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        groups: ['registration'],
        message: "Veuillez renseigner votre prénom.",
    )]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner votre nom de famille.",
    )]
    private ?string $lastName = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner votre adresse email.",
    )]
    #[Assert\Email(
        message: "Veuillez renseigner une adresse email valide.",
    )]
    private ?string $email = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(
        groups: ['registration'],
        message: "Veuillez renseigner un mot de passe.",
    )]
    private ?string $password = null;

    #[Assert\NotBlank(
        groups: ['registration'],
        message: "Veuillez renseigner un mot de passe.",
    )]
    #[Assert\EqualTo(
        propertyPath: "password",
        message: "Veuillez renseigner le même mot de passe que précédemment"
    )]
    private ?string $passwordConfirmation = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url(
        message: "Veuillez renseigner une URL valide.",
    )]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner une courte description à votre sujet.",
    )]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "La courte description doit comporter au minimum {{ limit }} caractères",
        maxMessage: "La courte description doit comporter au maximum {{ limit }} caractères",
    )]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner une description détaillée à votre sujet.",
    )]
    #[Assert\Length(
        min: 100,
        minMessage: "La description détaillée doit comporter au minimum {{ limit }} caractères",
    )]
    private ?string $description = null;

    #[ORM\OneToMany(targetEntity: Ad::class, mappedBy: 'manager', orphanRemoval: true)]
    private Collection $ads;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'booker')]
    private Collection $bookings;

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstName . ' ' . $this->lastName);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getFullName(): ?string
    {
        return "{$this->firstName} {$this->lastName}";
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(string $passwordConfirmation): static
    {
        $this->passwordConfirmation = $passwordConfirmation;

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
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): static
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): static
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setManager($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getManager() === $this) {
                $ad->setManager(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
            }
        }

        return $this;
    }
}
