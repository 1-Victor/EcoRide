<?php

namespace App\Controller;

use App\Repository\CarSharingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/recherche', name: 'app_search')]
    public function search(Request $request, CarSharingsRepository $repo): Response
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $date = $request->query->get('date');

        $eco = $request->query->getBoolean('eco');
        $maxPrice = $request->query->get('max_price');
        $maxDuration = $request->query->get('max_duration');
        $minRating = $request->query->get('min_rating');

        $carSharings = [];
        $alternative = null;

        if ($from && $to && $date) {
            $dateObj = new \DateTime($date);
            $results = $repo->searchByCriteria($from, $to, $dateObj, [
                'eco' => $eco,
                'max_price' => $maxPrice,
                'min_rating' => $minRating,
            ]);

            if (!empty($maxDuration)) {
                $carSharings = array_filter($results, function ($trajet) use ($maxDuration) {
                    $interval = $trajet->getDateStart()->diff($trajet->getDateEnd());
                    $minutes = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
                    return $minutes <= $maxDuration;
                });
            } else {
                $carSharings = $results;
            }

            if (empty($carSharings)) {
                $alternative = $repo->getClosestCarSharingAfterDate($from, $to, $dateObj);
            }
        }

        return $this->render('search/results.html.twig', [
            'from' => $from,
            'to' => $to,
            'date' => new \DateTime($date),
            'eco' => $eco,
            'max_price' => $maxPrice,
            'max_duration' => $maxDuration,
            'min_rating' => $minRating,
            'carSharings' => $carSharings, // ⚠️ c’est cette ligne qui manquait ou était vide
            'alternative' => $alternative,
        ]);
    }
}
