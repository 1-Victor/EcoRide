<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Form\CarSharingType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CarSharingController extends AbstractController
{
    #[Route('/trajet/nouveau', name: 'app_car_sharing_new')]
    #[IsGranted('ROLE_USER')] // accessible uniquement aux utilisateurs connectés
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        // Vérifier que le chauffeur a assez de crédits
        if ($user->getCredit() < 2) {
            $this->addFlash('danger', 'Vous n’avez pas assez de crédits pour créer un trajet.');
            return $this->redirectToRoute('app_home');
        }

        $carSharing = new CarSharings();
        $form = $this->createForm(CarSharingType::class, $carSharing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $carSharing->setUser($user);
            $carSharing->setCreatedAt(new \DateTime());
            $carSharing->setUpdatedAt(new \DateTime());
            $carSharing->setAvailablePlaces($carSharing->getTotalPlaces());
            $carSharing->setEcoFriendly(
                $carSharing->getVehicle()->getEnergy()->getName() === 'Électrique'
            );

            // Retirer 2 crédits au chauffeur
            $user->setCredit($user->getCredit() - 2);

            $em->persist($carSharing);
            $em->flush();

            $this->addFlash('success', 'Votre trajet a bien été créé 🎉');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('car_sharing/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/trajet/{id}', name: 'app_trajet_show')]
    public function show(CarSharings $carSharing): Response
    {
        return $this->render('car_sharing/show.html.twig', [
            'carSharing' => $carSharing,
        ]);
    }
}
