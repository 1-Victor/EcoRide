<?php

namespace App\Entity;

use App\Repository\EnergiesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\OneToMany(mappedBy: 'energy', targetEntity: Vehicles::class)]
    private Collection $vehicles;

    public function __construct()
    {
        $this->vehicles = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Vehicles>
     */
    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicles $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setEnergy($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicles $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            if ($vehicle->getEnergy() === $this) {
                $vehicle->setEnergy(null);
            }
        }

        return $this;
    }
}
