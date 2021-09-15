<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Vendor;
use App\Service\Helper;
use App\Service\ReservationService;
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
        $reservations = $this->getDoctrine()->getRepository(Reservation::class)->findAll();

        return new Response(Helper::serialize($reservations), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/vendors", name="vendors_list", methods="get")
     */
    public function vendors(): Response
    {
        $vendors = $this->getDoctrine()->getRepository(Vendor::class)->findAll();

        return new Response(Helper::serialize($vendors), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * @Route("/reservations/store", name="reservation_store", methods="post")
     */
    public function store(Request $request, ReservationService $rs): Response
    {
        $vendor = $this->getDoctrine()
            ->getRepository(Vendor::class)
            ->find($request->request->get('vendor_id'));
        $rs->makeReservation($vendor, $request->request->get('date'));

        return new Response(Helper::serialize($rs->getMessages()), Response::HTTP_OK, [
            'Content-Type' => 'application/json',
        ]);
    }
}
