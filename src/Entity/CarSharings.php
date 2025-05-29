<?php

namespace App\Entity;

use App\Repository\CarSharingsRepository;
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
    private ?string $start_address = null;

    #[ORM\Column(length: 255)]
    private ?string $end_address = null;

    #[ORM\Column]
    private ?\DateTime $date_start = null;

    #[ORM\Column]
    private ?\DateTime $date_end = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $total_places = null;

    #[ORM\Column]
    private ?int $available_places = null;

    #[ORM\Column]
    private ?bool $eco_friendly = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\Column]
    private ?\DateTime $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartAddress(): ?string
    {
        return $this->start_address;
    }

    public function setStartAddress(string $start_address): static
    {
        $this->start_address = $start_address;

        return $this;
    }

    public function getEndAddress(): ?string
    {
        return $this->end_address;
    }

    public function setEndAddress(string $end_address): static
    {
        $this->end_address = $end_address;

        return $this;
    }

    public function getDateStart(): ?\DateTime
    {
        return $this->date_start;
    }

    public function setDateStart(\DateTime $date_start): static
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDateEnd(): ?\DateTime
    {
        return $this->date_end;
    }

    public function setDateEnd(\DateTime $date_end): static
    {
        $this->date_end = $date_end;

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
        return $this->total_places;
    }

    public function setTotalPlaces(int $total_places): static
    {
        $this->total_places = $total_places;

        return $this;
    }

    public function getAvailablePlaces(): ?int
    {
        return $this->available_places;
    }

    public function setAvailablePlaces(int $available_places): static
    {
        $this->available_places = $available_places;

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

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTime $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
