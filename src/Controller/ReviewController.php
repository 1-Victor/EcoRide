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
  #[Route('/dashboard/employe/avis/{id}/supprimer', name: 'employee_review_delete', methods: ['POST'])]
  #[IsGranted('ROLE_EMPLOYEE')]
  public function deleteReview(Request $request, Reviews $review, EntityManagerInterface $em): Response
  {
    if ($this->isCsrfTokenValid('delete' . $review->getId(), $request->request->get('_token'))) {
      $em->remove($review);
      $em->flush();
      $this->addFlash('danger', 'Avis supprimé.');
    }

    return $this->redirectToRoute('employee_reviews');
  }

  #[Route('/trajet/{id}/noter', name: 'review_add')]
  #[IsGranted('ROLE_USER')]
  public function addReview(
    CarSharings $carSharing,
    Request $request,
    EntityManagerInterface $em,
    Security $security
  ): Response {
    $user = $security->getUser();

    // Vérifie que l'utilisateur a participé à ce trajet
    if (!$carSharing->getParticipants()->contains($user)) {
      $this->addFlash('danger', 'Vous ne pouvez noter ce trajet car vous n’y avez pas participé.');
      return $this->redirectToRoute('app_home');
    }

    // Vérifie si un avis a déjà été laissé
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
      $review->setTargetUser($carSharing->getUser());
      $review->setCarSharing($carSharing);
      $review->setUser($carSharing->getUser());
      $review->setIsValidated(false); // En attente de validation
      $review->setCreatedAt(new \DateTime());

      $em->persist($review);
      $em->flush();

      $this->addFlash('success', 'Merci pour votre avis ! Il sera publié après validation.');
      return $this->redirectToRoute('app_trajet_show', ['id' => $carSharing->getId()]);
    }

    return $this->render('review/form.html.twig', [
      'form' => $form,
      'carSharing' => $carSharing,
    ]);
  }

  #[Route('/dashboard/employe/avis', name: 'employee_reviews')]
  #[IsGranted('ROLE_EMPLOYEE')]
  public function reviewDashboard(EntityManagerInterface $em): Response
  {
    $reviews = $em->getRepository(Reviews::class)->findBy(['is_validated' => false]);

    return $this->render('employee/reviews_dashboard.html.twig', [
      'reviews' => $reviews,
    ]);
  }

  #[Route('/dashboard/employe/avis/{id}/valider', name: 'employee_review_validate')]
  #[IsGranted('ROLE_EMPLOYEE')]
  public function validateReview(Reviews $review, EntityManagerInterface $em): Response
  {
    $review->setIsValidated(true);
    $em->flush();

    $this->addFlash('success', 'Avis validé avec succès.');
    return $this->redirectToRoute('employee_reviews');
  }
}
