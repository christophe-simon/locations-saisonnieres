<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $booker = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ad $ad = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner la date de début de réservation",
    )]
    #[Assert\Type(
        type: "DateTimeImmutable"
    )]
    #[Assert\GreaterThan(
        value: 'today',
        message: "La date de début de réservation doit être postérieure à celle d'aujourd'hui"

    )]
    private ?DateTimeImmutable $startsOn = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    #[Assert\NotBlank(
        message: "Veuillez renseigner la date de fin de réservation",
    )]
    #[Assert\Type(
        type: "DateTimeImmutable"
    )]
    #[Assert\GreaterThan(
        propertyPath: 'startsOn',
        message: "La date de fin de réservation doit être postérieure à celle de début de réservation"

    )]
    private ?DateTimeImmutable $endsOn = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new DateTimeImmutable();
        }

        if (empty($this->amount)) {
            $this->amount = $this->ad->getPrice() * $this->getDuration();
        }
    }

    public function getDuration(): int
    {
        $diff = $this->endsOn->diff($this->startsOn);
        return $diff->days;
    }

    public function isABookablePeriod(): bool
    {
        // Get days that are already booked for the ad
        $unavailableDays = $this->ad->getUnavailableDays();

        // Compare chosen dates with unavailable days
        $bookingDays = $this->getDays();

        $formatDay = function ($day) {
            return $day->format('Y-m-d');
        };

        // Get the days in strings
        $days = array_map($formatDay, $bookingDays);
        $unavailable = array_map($formatDay, $unavailableDays);

        foreach ($days as $day) {
            if (array_search($day, $unavailable) !== false) return false;
        }

        return true;
    }

    /**
     * Enables to get an array of the days of the booking
     *
     * @return array An array of DateTimeImmutable objects representing the days of the booking
     */
    public function getDays(): array
    {
        $result = range(
            $this->startsOn->getTimestamp(),
            $this->endsOn->getTimestamp(),
            24 * 60 * 60
        );

        $days = array_map(function ($dayTimestamp) {
            return new DateTimeImmutable(date('Y-m-d', $dayTimestamp));
        }, $result);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): static
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): static
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartsOn(): ?DateTimeImmutable
    {
        return $this->startsOn;
    }

    public function setStartsOn(DateTimeImmutable $startsOn): static
    {
        $this->startsOn = $startsOn;

        return $this;
    }

    public function getEndsOn(): ?DateTimeImmutable
    {
        return $this->endsOn;
    }

    public function setEndsOn(DateTimeImmutable $endsOn): static
    {
        $this->endsOn = $endsOn;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
