<?php

namespace App\Entity;

use App\Repository\PassengerConfirmationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PassengerConfirmationRepository::class)]
class PassengerConfirmation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'passengerConfirmations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $passenger = null;

    #[ORM\ManyToOne(inversedBy: 'passengerConfirmations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CarSharings $carSharing = null;

    #[ORM\Column(nullable: true)]
    private ?bool $confirmed = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassenger(): ?User
    {
        return $this->passenger;
    }

    public function setPassenger(?User $passenger): static
    {
        $this->passenger = $passenger;

        return $this;
    }

    public function getCarSharing(): ?CarSharings
    {
        return $this->carSharing;
    }

    public function setCarSharing(?CarSharings $carSharing): static
    {
        $this->carSharing = $carSharing;

        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(?bool $confirmed): static
    {
        $this->confirmed = $confirmed;

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
