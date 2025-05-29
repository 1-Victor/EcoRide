<?php

namespace App\Entity;

use App\Repository\CarSharingStatesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CarSharingStatesRepository::class)]
class CarSharingStates
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: CarSharings::class)]
    private Collection $carSharings;

    public function __construct()
    {
        $this->carSharings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, CarSharings>
     */
    public function getCarSharings(): Collection
    {
        return $this->carSharings;
    }

    public function addCarSharing(CarSharings $carSharing): static
    {
        if (!$this->carSharings->contains($carSharing)) {
            $this->carSharings->add($carSharing);
            $carSharing->setStatus($this);
        }

        return $this;
    }

    public function removeCarSharing(CarSharings $carSharing): static
    {
        if ($this->carSharings->removeElement($carSharing)) {
            if ($carSharing->getStatus() === $this) {
                $carSharing->setStatus(null);
            }
        }

        return $this;
    }
}
