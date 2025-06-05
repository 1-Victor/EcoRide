<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Entity\PassengerConfirmation;
use App\Form\PassengerConfirmationType;
use App\Repository\PassengerConfirmationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PassengerConfirmationController extends AbstractController
{
  #[Route('/confirmation/{token}', name: 'passenger_confirm_token')]
  public function confirmByToken(
    string $token,
    PassengerConfirmationRepository $repo,
    EntityManagerInterface $em
  ): Response {
    // 🔎 Recherche de la confirmation via le token
    $confirmation = $repo->findOneBy(['token' => $token]);

    if (!$confirmation) {
      throw $this->createNotFoundException('Lien de confirmation invalide ou expiré.');
    }

    // 🔁 Vérifie si déjà confirmé
    if ($confirmation->isConfirmed()) {
      $this->addFlash('info', 'Vous avez déjà confirmé ce trajet.');
      return $this->redirectToRoute('app_home');
    }

    // ✅ Marque comme confirmé
    $confirmation->setIsConfirmed(true);
    $confirmation->setCreatedAt(new \DateTime());
    $em->persist($confirmation);

    $carSharing = $confirmation->getCarSharing();

    // 🎯 Vérifie si tous les passagers ont confirmé
    if ($carSharing->isFullyConfirmed()) {
      $chauffeur = $carSharing->getUser();
      $gain = $carSharing->getPrice() * count($carSharing->getParticipants());
      $chauffeur->setCredit($chauffeur->getCredit() + $gain);

      $em->persist($chauffeur);
      $this->addFlash('success', 'Merci ! Tous les passagers ont confirmé. Le chauffeur a été crédité ✅');
    } else {
      $this->addFlash('success', 'Merci pour votre confirmation 🙏');
    }

    $em->flush();

    return $this->redirectToRoute('app_home');
  }

  #[Route('/covoiturage/{id}/confirmer', name: 'passenger_confirmation')]
  public function confirm(
    CarSharings $carSharing,
    Request $request,
    EntityManagerInterface $em,
    PassengerConfirmationRepository $repo
  ): Response {
    $user = $this->getUser();

    if (!$user || !$carSharing->getParticipants()->contains($user)) {
      $this->addFlash('danger', 'Vous ne participez pas à ce trajet.');
      return $this->redirectToRoute('app_home');
    }

    $existing = $repo->findOneBy([
      'carSharing' => $carSharing,
      'passenger' => $user,
    ]);

    if ($existing) {
      $this->addFlash('info', 'Vous avez déjà confirmé ce trajet.');
      return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
    }

    $confirmation = new PassengerConfirmation();
    $confirmation->setPassenger($user);
    $confirmation->setCarSharing($carSharing);
    $confirmation->setToken(bin2hex(random_bytes(32))); // 🔐 Génère un token sécurisé

    $form = $this->createForm(PassengerConfirmationType::class, $confirmation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      if ($confirmation->isConfirmed() === false && !$confirmation->getComment()) {
        $this->addFlash('danger', 'Un commentaire est requis si vous signalez un problème.');
        return $this->render('passenger_confirmation/confirm.html.twig', [
          'form' => $form->createView(),
          'carSharing' => $carSharing,
        ]);
      }

      $confirmation->setCreatedAt(new \DateTime());

      $em->persist($confirmation);
      $em->flush();

      if (!$confirmation->isConfirmed()) {
        $this->addFlash('warning', 'Merci, nous avons bien noté votre signalement. Un employé va vérifier.');
        return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
      }

      if ($carSharing->isFullyConfirmed()) {
        $chauffeur = $carSharing->getUser();
        $gain = $carSharing->getPrice() * count($carSharing->getParticipants());
        $chauffeur->setCredit($chauffeur->getCredit() + $gain);
        $em->persist($chauffeur);
        $em->flush();

        $this->addFlash('success', 'Tous les passagers ont confirmé, le chauffeur a été crédité.');
      } else {
        $this->addFlash('success', 'Merci pour votre confirmation.');
      }

      return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
    }

    return $this->render('passenger_confirmation/confirm.html.twig', [
      'form' => $form->createView(),
      'carSharing' => $carSharing,
    ]);
  }
}
