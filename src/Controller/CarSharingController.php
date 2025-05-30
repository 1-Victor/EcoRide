<?php

namespace App\Controller;

use App\Entity\CarSharings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CarSharingController extends AbstractController
{
    #[Route('/trajet/{id}', name: 'app_trajet_show')]
    public function show(CarSharings $carSharing): Response
    {
        return $this->render('car_sharing/show.html.twig', [
            'carSharing' => $carSharing,
        ]);
    }
}
