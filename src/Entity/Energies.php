<?php

namespace App\Entity;

use App\Repository\EnergiesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EnergiesRepository::class)]
class Energies
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $co2_emission = null;

    #[ORM\Column]
    private ?int $autonomy_km = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCo2Emission(): ?int
    {
        return $this->co2_emission;
    }

    public function setCo2Emission(int $co2_emission): static
    {
        $this->co2_emission = $co2_emission;

        return $this;
    }

    public function getAutonomyKm(): ?int
    {
        return $this->autonomy_km;
    }

    public function setAutonomyKm(int $autonomy_km): static
    {
        $this->autonomy_km = $autonomy_km;

        return $this;
    }
}
