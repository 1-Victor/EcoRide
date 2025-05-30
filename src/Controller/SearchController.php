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
        // ⚠️ Si le bouton reset est coché, rediriger vers l'accueil
        if ($request->query->get('reset')) {
            return $this->redirectToRoute('app_home');
        }

        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $dateString = $request->query->get('date');
        $date = $dateString ? \DateTime::createFromFormat('Y-m-d', $dateString) : null;

        // 🔄 Filtres avancés
        $eco = $request->query->getBoolean('eco');
        $maxPrice = $request->query->get('max_price');
        $maxDuration = $request->query->get('max_duration');
        $minRating = $request->query->get('min_rating');

        $results = [];
        $alternative = null;

        if ($from && $to && $date) {
            $results = $repo->searchByCriteria($from, $to, $date, [
                'eco' => $eco,
                'max_price' => $maxPrice,
                'max_duration' => $maxDuration,
                'min_rating' => $minRating,
            ]);

            if (empty($results)) {
                $alternative = $repo->getClosestCarSharingAfterDate($from, $to, $date);
            }
        }

        return $this->render('search/results.html.twig', [
            'from' => $from,
            'to' => $to,
            'date' => $date,
            'results' => $results,
            'alternative' => $alternative,
            'eco' => $eco,
            'max_price' => $maxPrice,
            'max_duration' => $maxDuration,
            'min_rating' => $minRating,
        ]);
    }
}
