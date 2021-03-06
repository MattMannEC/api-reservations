<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Vendor;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

Class ReservationService
{
    private $em;
    private $messages = [];

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function isAvailable($date): bool
    {
        $repository = $this->em->getRepository(Reservation::class);
        $datetime = new DateTime($date);
        $reservations = $repository->findBy(
            ['date' => $datetime],
        );
        return count($reservations) < $this->getCapacity($date);
    }

    public function isWeekday($date): bool
    {
        $datetime = new DateTime($date);
        return in_array($datetime->format('N'), [1, 2, 3, 4, 5]);
    }

    public function getCapacity($date): int
    {
        $datetime = new DateTime($date);
        if ($datetime->format('N') === '5') {
            return 6;
        };
        return 7;
    }

    public function makeReservation(Vendor $vendor, $date): void
    {
        if ($this->canBook($vendor, $date)) {
            $reservation = new Reservation();
            $reservation->setVendor($vendor);
            $datetime = new DateTime($date);
            $reservation->setDate($datetime);
            $this->em->persist($reservation);
            $this->em->flush();
            $this->messages[] = "Success";
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Check if this vendor is allowed to make this reservation
     */
    public function canBook(Vendor $vendor, $date): bool
    {
        if ($vendor->hasReservation($date)) {
            $this->messages[] = "You have already reserved for this day...";
        }
        if ($vendor->hasThree($date)) {
            $this->messages[] = "You have already reserved three for that week...";
        }
        if (!$this->isAvailable($date)) {
            $this->messages[] = "This day is fully booked...";
        }
        if (!$this->isWeekday($date)) {
            $this->messages[] = "You cannot book for the weekend...";
        }

        return empty($this->messages);
    }
}
