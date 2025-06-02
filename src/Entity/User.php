<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[UniqueEntity(fields: ['username'], message: 'Ce pseudo est déjà utilisé.')]
#[UniqueEntity(fields: ['phone'], message: 'Ce numéro est déjà utilisé.')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Ignore]
    #[Vich\UploadableField(mapping: 'profile_image', fileNameProperty: 'imageName', size: 'imageSize')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?int $imageSize = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\Column(length: 50)]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $lastname = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?int $credit = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Suspensions::class, orphanRemoval: true)]
    private Collection $suspensions;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reservations::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Reviews::class)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Vehicles::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $vehicles;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserPreferences::class, orphanRemoval: true)]
    private Collection $userPreferences;

    #[ORM\ManyToMany(targetEntity: CarSharings::class, inversedBy: 'participants')]
    private Collection $carSharingsParticipated;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CarSharings::class)]
    private Collection $carSharings;

    #[ORM\OneToMany(mappedBy: 'targetUser', targetEntity: Reviews::class)]
    private Collection $reviewsReceived;

    private ?string $plainPassword = null;

    /**
     * @var Collection<int, PassengerConfirmation>
     */
    #[ORM\OneToMany(targetEntity: PassengerConfirmation::class, mappedBy: 'passenger')]
    private Collection $passengerConfirmations;

    public function __construct()
    {
        $this->suspensions = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->credit = 20;
        $this->vehicles = new ArrayCollection();
        $this->userPreferences = new ArrayCollection();
        $this->carSharingsParticipated = new ArrayCollection();
        $this->carSharings = new ArrayCollection();
        $this->reviewsReceived = new ArrayCollection();
        $this->passengerConfirmations = new ArrayCollection();
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTime();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): static
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(int $credit): static
    {
        $this->credit = $credit;
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

    public function getVehicles(): Collection
    {
        return $this->vehicles;
    }

    public function addVehicle(Vehicles $vehicle): static
    {
        if (!$this->vehicles->contains($vehicle)) {
            $this->vehicles->add($vehicle);
            $vehicle->setUser($this);
        }

        return $this;
    }

    public function removeVehicle(Vehicles $vehicle): static
    {
        if ($this->vehicles->removeElement($vehicle)) {
            if ($vehicle->getUser() === $this) {
                $vehicle->setUser(null);
            }
        }

        return $this;
    }

    public function getUserPreferences(): Collection
    {
        return $this->userPreferences;
    }

    public function addUserPreference(UserPreferences $preference): static
    {
        if (!$this->userPreferences->contains($preference)) {
            $this->userPreferences->add($preference);
            $preference->setUser($this);
        }
        return $this;
    }

    public function removeUserPreference(UserPreferences $preference): static
    {
        if ($this->userPreferences->removeElement($preference)) {
            if ($preference->getUser() === $this) {
                $preference->setUser(null);
            }
        }
        return $this;
    }

    public function getSuspensions(): Collection
    {
        return $this->suspensions;
    }

    public function addSuspension(Suspensions $suspension): static
    {
        if (!$this->suspensions->contains($suspension)) {
            $this->suspensions[] = $suspension;
            $suspension->setUser($this);
        }
        return $this;
    }

    public function removeSuspension(Suspensions $suspension): static
    {
        if ($this->suspensions->removeElement($suspension)) {
            if ($suspension->getUser() === $this) {
                $suspension->setUser(null);
            }
        }
        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservations $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setUser($this);
        }
        return $this;
    }

    public function removeReservation(Reservations $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }
        return $this;
    }

    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Reviews $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setUser($this);
        }
        return $this;
    }

    public function removeReview(Reviews $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }
        return $this;
    }

    public function getCarSharingsParticipated(): Collection
    {
        return $this->carSharingsParticipated;
    }

    public function getCarSharings(): Collection
    {
        return $this->carSharings;
    }

    public function addCarSharingParticipation(CarSharings $carSharing): static
    {
        if (!$this->carSharingsParticipated->contains($carSharing)) {
            $this->carSharingsParticipated->add($carSharing);
            $carSharing->getParticipants()->add($this); // synchronisation inverse
        }

        return $this;
    }

    public function removeCarSharingParticipation(CarSharings $carSharing): static
    {
        if ($this->carSharingsParticipated->removeElement($carSharing)) {
            $carSharing->getParticipants()->removeElement($this); // synchronisation inverse
        }

        return $this;
    }

    public function getReviewsReceived(): Collection
    {
        return $this->reviewsReceived;
    }

    public function getAverageRating(): float
    {
        if ($this->reviewsReceived->isEmpty()) {
            return 0;
        }

        $total = 0;
        $count = 0;

        foreach ($this->reviewsReceived as $review) {
            if ($review->isValidated()) {
                $total += $review->getRating();
                $count++;
            }
        }

        return $count > 0 ? round($total / $count, 1) : 0;
    }

    /**
     * @return Collection<int, PassengerConfirmation>
     */
    public function getPassengerConfirmations(): Collection
    {
        return $this->passengerConfirmations;
    }

    public function addPassengerConfirmation(PassengerConfirmation $passengerConfirmation): static
    {
        if (!$this->passengerConfirmations->contains($passengerConfirmation)) {
            $this->passengerConfirmations->add($passengerConfirmation);
            $passengerConfirmation->setPassenger($this);
        }

        return $this;
    }

    public function removePassengerConfirmation(PassengerConfirmation $passengerConfirmation): static
    {
        if ($this->passengerConfirmations->removeElement($passengerConfirmation)) {
            // set the owning side to null (unless already changed)
            if ($passengerConfirmation->getPassenger() === $this) {
                $passengerConfirmation->setPassenger(null);
            }
        }

        return $this;
    }
}
