<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Vendor;
use App\Service\ReservationService;
use DateTime;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationsController extends AbstractController
{
    /**
     * @Route("/reservations", name="reservations_list", methods="get")
     */
    public function reservations(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Reservation::class);

        return new Response($this->serialize($repository->findAll()), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    public function serialize($data)
    {
        $seriaizer = SerializerBuilder::create()->build();

        return $seriaizer->serialize($data, 'json');
    }

    /**
     * @Route("/vendors", name="vendors_list", methods="get")
     */
    public function vendors(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Vendor::class);

        return new Response($this->serialize($repository->findAll()), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/reservations/store", name="reservation_store")
     */
    public function store(Request $request, ReservationService $rs)
    {
        $vendorRepository = $this->getDoctrine()->getRepository(Vendor::class);
        $vendor = $vendorRepository->find($request->request->get('vendor_id'));
        $date = $request->request->get('date');
        $rs->makeReservation($vendor, $date);

        return new Response($this->serialize($rs->messages), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
