<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Form\CarSharingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\UnicodeString;

final class CarSharingController extends AbstractController
{
    #[Route('/trajet/{id}/annuler-par-chauffeur', name: 'carsharing_cancel_by_driver')]
    #[IsGranted('ROLE_USER')]
    public function cancelAsDriver(
        CarSharings $carSharing,
        EntityManagerInterface $em,
        Security $security
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        // Vérifie que l'utilisateur est le conducteur
        if ($carSharing->getUser() !== $user) {
            $this->addFlash('danger', 'Vous ne pouvez pas annuler ce trajet.');
            return $this->redirectToRoute('app_driver_dashboard');
        }

        // Rembourse chaque participant
        foreach ($carSharing->getParticipants() as $participant) {
            $participant->setCredit($participant->getCredit() + $carSharing->getPrice());
            $participant->removeCarSharingParticipation($carSharing);
            $em->persist($participant);
        }

        // Supprime le trajet
        $em->remove($carSharing);
        $em->flush();

        $this->addFlash('success', 'Le trajet a été annulé. Tous les participants ont été remboursés.');
        return $this->redirectToRoute('app_driver_dashboard');
    }

    #[Route('/covoiturage/{id}/annuler', name: 'carsharing_cancel')]
    public function cancelParticipation(
        CarSharings $carSharing,
        EntityManagerInterface $em,
        Security $security,
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'utilisateur participe à ce covoiturage
        if (!$user->getCarSharingsParticipated()->contains($carSharing)) {
            $this->addFlash('error', 'Vous ne participez pas à ce covoiturage.');
            return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
        }

        // Supprimer la participation
        $user->removeCarSharingParticipation($carSharing);

        // Rendre les crédits
        $user->setCredit($user->getCredit() + $carSharing->getPrice());

        // Réajuster les places disponibles
        $carSharing->setAvailablePlaces($carSharing->getAvailablePlaces() + 1);

        $em->persist($user);
        $em->persist($carSharing);
        $em->flush();

        $this->addFlash('success', 'Votre participation a été annulée.');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/covoiturage/{id}/participer', name: 'carsharing_participate')]
    public function participate(
        CarSharings $carSharing,
        EntityManagerInterface $em,
        Security $security,
        Request $request
    ): Response {
        $user = $security->getUser();
        /** @var \App\Entity\User $user */

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($carSharing->getAvailablePlaces() <= 0 || $user->getCredit() < $carSharing->getPrice()) {
            $this->addFlash('error', 'Participation impossible : pas assez de crédits ou de places.');
            return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
        }

        // Étape de confirmation
        if ($request->query->get('confirm') !== 'true') {
            return $this->render('car_sharing/confirm_participation.html.twig', [
                'carSharing' => $carSharing,
            ]);
        }

        // Traitement de la participation
        $user->addCarSharingParticipation($carSharing);
        $user->setCredit($user->getCredit() - $carSharing->getPrice());
        $carSharing->setAvailablePlaces($carSharing->getAvailablePlaces() - 1);

        $em->persist($user);
        $em->persist($carSharing);
        $em->flush();

        $this->addFlash('success', 'Vous avez bien réservé ce covoiturage !');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/trajet/nouveau', name: 'app_car_sharing_new')]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

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

            // Villes automatiquement extraites
            $startCity = (new UnicodeString($carSharing->getStartAddress()))->afterLast(',')->trim()->title(true);
            $endCity = (new UnicodeString($carSharing->getEndAddress()))->afterLast(',')->trim()->title(true);
            $carSharing->setStartCity($startCity);
            $carSharing->setEndCity($endCity);

            // État initial
            $status = $em->getRepository(\App\Entity\CarSharingStates::class)->findOneBy(['status' => 'En attente']);
            $carSharing->setStatus($status);

            // Débit du chauffeur
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
