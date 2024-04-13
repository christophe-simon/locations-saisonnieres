<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use App\Repository\AdRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: 'title',
    message: "Une annonce contient déjà un titre similaire"
)]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères",
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 20,
        minMessage: "L'introduction doit contenir au moins {{ limit }} caractères",
    )]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 100,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url()]
    private ?string $coverPicture = null;

    #[ORM\Column]
    private ?int $rooms = null;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: Picture::class, orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $pictures;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $manager = null;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'ad')]
    private Collection $bookings;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug()
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * Enables to get an array of the days that are not available any more for this ad
     *
     * @return array An array of DateTimeImmutable objects representing booked days
     */
    public function getUnavailableDays()
    {
        $unavailableDays = [];

        foreach ($this->bookings as $booking) {
            $daysInTimestamp = range(
                $booking->getStartsOn()->getTimestamp(),
                $booking->getEndsOn()->getTimestamp(),
                24 * 60 * 60
            );

            $days = array_map(function ($dayTimestamp) {
                return new DateTimeImmutable(date('Y-m-d', $dayTimestamp));
            }, $daysInTimestamp);

            $unavailableDays = array_merge($unavailableDays, $days);
        }

        return $unavailableDays;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverPicture(): ?string
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(string $coverPicture): static
    {
        $this->coverPicture = $coverPicture;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setAd($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getAd() === $this) {
                $picture->setAd(null);
            }
        }

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

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
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }
}
