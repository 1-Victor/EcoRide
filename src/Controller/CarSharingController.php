<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Entity\CarSharingStates;
use App\Entity\PassengerConfirmation;
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

    #[Route('/trajet/{id}/demarrer', name: 'carsharing_start')]
    #[IsGranted('ROLE_USER')]
    public function start(CarSharings $carSharing, EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        // Vérifie que l'utilisateur est bien le conducteur
        if ($user !== $carSharing->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas démarrer ce trajet.');
            return $this->redirectToRoute('app_driver_dashboard');
        }

        // Vérifie que le trajet est bien en attente
        if ($carSharing->getStatus()->getStatus() !== 'En attente') {
            $this->addFlash('warning', 'Ce trajet n’est pas en attente.');
            return $this->redirectToRoute('app_driver_dashboard');
        }

        // Mise à jour du statut en "En cours"
        $status = $em->getRepository(CarSharingStates::class)->findOneBy(['status' => 'En cours']);
        if (!$status) {
            throw $this->createNotFoundException('Statut "En cours" non trouvé.');
        }

        $carSharing->setStatus($status);
        $em->flush();

        $this->addFlash('success', 'Trajet démarré avec succès.');
        return $this->redirectToRoute('app_driver_dashboard');
    }

    #[Route('/trajet/{id}/terminer', name: 'carsharing_finish')]
    #[IsGranted('ROLE_USER')]
    public function finish(
        CarSharings $carSharing,
        EntityManagerInterface $em,
        Security $security,
        \App\Service\MailerService $mailerService // ✅ Injection du service mailer
    ): Response {
        $user = $security->getUser();

        // 🔒 Vérifie que l'utilisateur est bien le conducteur
        if ($user !== $carSharing->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas terminer ce trajet.');
            return $this->redirectToRoute('app_driver_dashboard');
        }

        // 🔁 Vérifie que le trajet est bien en cours
        if ($carSharing->getStatus()->getStatus() !== 'En cours') {
            $this->addFlash('warning', 'Ce trajet n’est pas en cours.');
            return $this->redirectToRoute('app_driver_dashboard');
        }

        // ✅ Mise à jour du statut en "Terminé"
        $status = $em->getRepository(CarSharingStates::class)->findOneBy(['status' => 'Terminé']);
        if (!$status) {
            throw $this->createNotFoundException('Statut "Terminé" non trouvé.');
        }

        $carSharing->setStatus($status);
        $em->flush();

        // 📩 Envoi d’un e-mail à chaque passager
        foreach ($carSharing->getParticipants() as $passenger) {
            $confirmation = new PassengerConfirmation();
            $confirmation->setPassenger($passenger);
            $confirmation->setCarSharing($carSharing);
            $confirmation->setToken(bin2hex(random_bytes(32))); // 🔐 Token sécurisé
            $confirmation->setIsConfirmed(false);
            $confirmation->setCreatedAt(new \DateTime());

            $em->persist($confirmation);

            // 📩 Envoi de l’e-mail avec le lien contenant le token
            $mailerService->sendTripConfirmation($passenger, $carSharing, $confirmation->getToken());
        }

        $this->addFlash('success', 'Trajet terminé. En attente de validation des passagers.');
        return $this->redirectToRoute('app_driver_dashboard');
    }
}
