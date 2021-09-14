<?php

namespace App\Entity;

use App\Repository\VendorRepository;
use App\Service\Helper;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VendorRepository::class)
 */
class Vendor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="vendor", orphanRemoval=true)
     */
    private $reservations;

    private $messages = [];

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMessages(): ?array
    {
        return $this->messages;
    }

    public function setMessages(string $message): self
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setVendor($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getVendor() === $this) {
                $reservation->setVendor(null);
            }
        }

        return $this;
    }

    public function hasThree($date)
    {
        $result = Helper::getWeekStartAndEnd($date);

        return (count($this->reservations->filter(function ($p) use ($result) {
            return $p->getDate()->format('Y-m-d') >= $result['week_start'] &&
                $p->getDate()->format('Y-m-d') <= $result['week_end'];
        }))) >= 3;
    }

    public function hasReservation($date)
    {
        $datetime = new DateTime($date);

        return $this->reservations->exists(function ($key, $element) use ($datetime) {
            return $element->getDate()->format('Y-m-d') === $datetime->format('Y-m-d');
        });
    }

    /**
     * Check if this vendor is allowed to make this reservation
     */
    public function canBook($date): bool
    {
        if ($this->hasReservation($date)) {
            $this->messages[] = "You have already reserved for this day...";
        }
        if ($this->hasThree($date)) {
            $this->messages[] = "You have already reserved three for that week...";
        }

        return empty($this->messages);
    }
}
