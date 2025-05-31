<?php

namespace App\Controller;

use App\Repository\CarSharingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CarSharingsRepository $carSharingsRepository): Response
    {
        $carSharings = $carSharingsRepository->findBy([], ['dateStart' => 'ASC']);

        return $this->render('home/home.html.twig', [
            'message' => 'Bienvenue sur la page d’accueil !',
            'carSharings' => $carSharings,
        ]);
    }
}
