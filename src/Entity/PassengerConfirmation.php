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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    #[ORM\Column]
    private ?bool $isConfirmed = null;

    #[ORM\Column(length: 20)]
    private string $status = 'pending'; // pending, validated, rejected

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;
        return $this;
    }

    public function getIsConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function setIsConfirmed(bool $isConfirmed): static
    {
        $this->isConfirmed = $isConfirmed;
        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->isConfirmed;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
