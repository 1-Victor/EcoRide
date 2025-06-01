<?php

namespace App\Controller;

use App\Entity\CarSharings;
use App\Entity\Reviews;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReviewController extends AbstractController
{
  #[Route('/trajet/{id}/noter', name: 'review_add')]
  #[IsGranted('ROLE_USER')]
  public function addReview(
    CarSharings $carSharing,
    Request $request,
    EntityManagerInterface $em,
    Security $security
  ): Response {
    $user = $security->getUser();

    // Vérifie que l'utilisateur a participé
    if (!$carSharing->getParticipants()->contains($user)) {
      $this->addFlash('danger', 'Vous ne pouvez noter ce trajet car vous n’y avez pas participé.');
      return $this->redirectToRoute('app_home');
    }

    // Vérifie s'il a déjà noté ce conducteur pour ce trajet
    $existingReview = $em->getRepository(Reviews::class)->findOneBy([
      'author' => $user,
      'carSharing' => $carSharing,
    ]);
    if ($existingReview) {
      $this->addFlash('info', 'Vous avez déjà laissé un avis pour ce trajet.');
      return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
    }

    $review = new Reviews();
    $form = $this->createForm(ReviewType::class, $review);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $review->setAuthor($user);
      $review->setTargetUser($carSharing->getUser()); // le conducteur
      $review->setCarSharing($carSharing);
      $review->setUser($carSharing->getUser()); // liaison supplémentaire
      $review->setIsValidated(true); // validé par défaut
      $review->setCreatedAt(new \DateTime());

      $em->persist($review);
      $em->flush();

      $this->addFlash('success', 'Merci pour votre avis !');
      return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
    }

    return $this->render('review/form.html.twig', [
      'form' => $form,
      'carSharing' => $carSharing,
    ]);
  }
}
