<?php

namespace App\Entity;

use App\Repository\UserPreferencesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserPreferencesRepository::class)]
class UserPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $smokerAllowed = null;

    #[ORM\Column(length: 50)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: "userPreferences")]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Preferences $preference = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isSmokerAllowed(): ?bool
    {
        return $this->smokerAllowed;
    }

    public function setSmokerAllowed(?bool $smokerAllowed): static
    {
        $this->smokerAllowed = $smokerAllowed;
        return $this;
    }

    #[ORM\Column(nullable: true)]
    private ?bool $petsAllowed = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $musicPreference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $talkingPreference = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $airConditioning = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $latenessTolerance = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $customPreferences = null;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;
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

    public function getPreference(): ?Preferences
    {
        return $this->preference;
    }

    public function setPreference(?Preferences $preference): static
    {
        $this->preference = $preference;
        return $this;
    }


    public function isPetsAllowed(): ?bool
    {
        return $this->petsAllowed;
    }

    public function setPetsAllowed(?bool $petsAllowed): static
    {
        $this->petsAllowed = $petsAllowed;
        return $this;
    }

    public function getMusicPreference(): ?string
    {
        return $this->musicPreference;
    }

    public function setMusicPreference(?string $musicPreference): static
    {
        $this->musicPreference = $musicPreference;
        return $this;
    }

    public function getTalkingPreference(): ?string
    {
        return $this->talkingPreference;
    }

    public function setTalkingPreference(?string $talkingPreference): static
    {
        $this->talkingPreference = $talkingPreference;
        return $this;
    }

    public function isAirConditioning(): ?bool
    {
        return $this->airConditioning;
    }

    public function setAirConditioning(?bool $airConditioning): static
    {
        $this->airConditioning = $airConditioning;
        return $this;
    }

    public function getLatenessTolerance(): ?string
    {
        return $this->latenessTolerance;
    }

    public function setLatenessTolerance(?string $latenessTolerance): static
    {
        $this->latenessTolerance = $latenessTolerance;
        return $this;
    }

    public function getCustomPreferences(): ?string
    {
        return $this->customPreferences;
    }

    public function setCustomPreferences(?string $customPreferences): static
    {
        $this->customPreferences = $customPreferences;
        return $this;
    }
}
