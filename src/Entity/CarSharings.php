<?php

namespace App\Entity;

use App\Repository\CarSharingsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarSharingsRepository::class)]
class CarSharings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $startAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $startCity = null;

    #[ORM\Column(length: 255)]
    private ?string $endAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $endCity = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateEnd = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $totalPlaces = null;

    #[ORM\Column]
    private ?int $availablePlaces = null;

    #[ORM\Column]
    private ?bool $eco_friendly = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'carSharings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'carSharings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Vehicles $vehicle = null;

    #[ORM\ManyToOne(inversedBy: 'carSharings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarSharingStates $status = null;

    #[ORM\OneToMany(mappedBy: 'carSharing', targetEntity: Reservations::class, orphanRemoval: true)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'carSharing', targetEntity: Reviews::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\ManyToMany(mappedBy: 'carSharingsParticipated', targetEntity: User::class)]
    private Collection $participants;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAddress(): ?string
    {
        return $this->startAddress;
    }

    public function setStartAddress(string $startAddress): static
    {
        $this->startAddress = $startAddress;
        return $this;
    }

    public function getEndAddress(): ?string
    {
        return $this->endAddress;
    }

    public function setEndAddress(string $endAddress): static
    {
        $this->endAddress = $endAddress;
        return $this;
    }

    public function getStartCity(): ?string
    {
        return $this->startCity;
    }

    public function setStartCity(string $startCity): static
    {
        $this->startCity = $startCity;
        return $this;
    }

    public function getEndCity(): ?string
    {
        return $this->endCity;
    }

    public function setEndCity(string $endCity): static
    {
        $this->endCity = $endCity;
        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $date_end): static
    {
        $this->dateEnd = $date_end;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getTotalPlaces(): ?int
    {
        return $this->totalPlaces;
    }

    public function setTotalPlaces(int $totalPlaces): static
    {
        $this->totalPlaces = $totalPlaces;
        return $this;
    }

    public function getAvailablePlaces(): ?int
    {
        return $this->availablePlaces;
    }

    public function setAvailablePlaces(int $availablePlaces): static
    {
        $this->availablePlaces = $availablePlaces;
        return $this;
    }

    public function isEcoFriendly(): ?bool
    {
        return $this->eco_friendly;
    }

    public function setEcoFriendly(bool $eco_friendly): static
    {
        $this->eco_friendly = $eco_friendly;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getVehicle(): ?Vehicles
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicles $vehicle): static
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    public function getStatus(): ?CarSharingStates
    {
        return $this->status;
    }

    public function setStatus(?CarSharingStates $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection<int, Reservations>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setCarSharing($this);
        }

        return $this;
    }

    public function removeReservation(Reservations $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getCarSharing() === $this) {
                $reservation->setCarSharing(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reviews>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setCarSharing($this);
        }

        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getCarSharing() === $this) {
                $review->setCarSharing(null);
            }
        }

        return $this;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function hasReviewFrom(User $user): bool
    {
        foreach ($this->reviews as $review) {
            if ($review->getAuthor() === $user) {
                return true;
            }
        }
        return false;
    }
}
