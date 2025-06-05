<?php

namespace App\Controller;

use App\Repository\PassengerConfirmationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

#[Route('/employee')]
#[AttributeIsGranted('ROLE_EMPLOYEE')]
class EmployeeSignalementController extends AbstractController
{
  #[Route('/signalements', name: 'employee_signalements')]
  public function index(PassengerConfirmationRepository $repo): Response
  {
    $signalements = $repo->findBy([
      'status' => 'pending',
      'isConfirmed' => false,
    ]);

    return $this->render('employee/signalements.html.twig', [
      'signalements' => $signalements,
    ]);
  }

  #[Route('/signalement/{id}/valider', name: 'employee_signalement_valider', methods: ['POST'])]
  public function valider(int $id, PassengerConfirmationRepository $repo, EntityManagerInterface $em): Response
  {
    $signalement = $repo->find($id);
    if (!$signalement) {
      throw $this->createNotFoundException('Signalement non trouvé.');
    }

    $signalement->setStatus('validated');

    // Vérifier s'il reste d'autres signalements "pending" ou "rejected" pour ce trajet
    $carSharing = $signalement->getCarSharing();
    $pendingReports = $repo->createQueryBuilder('p')
      ->where('p.carSharing = :carSharing')
      ->andWhere('p.status IN (:statuses)')
      ->setParameter('carSharing', $carSharing)
      ->setParameter('statuses', ['pending', 'rejected'])
      ->getQuery()
      ->getResult();

    if (count($pendingReports) === 0) {
      // Tous les signalements ont été traités -> rembourser le chauffeur
      $chauffeur = $carSharing->getUser();
      $gain = $carSharing->getPrice() * count($carSharing->getParticipants());
      $chauffeur->setCredit($chauffeur->getCredit() + $gain);
      $em->persist($chauffeur);
    }

    $em->flush();

    $this->addFlash('success', 'Signalement validé.');

    return $this->redirectToRoute('employee_signalements');
  }

  #[Route('/signalement/{id}/rejeter', name: 'employee_signalement_rejeter', methods: ['POST'])]
  public function rejeter(int $id, PassengerConfirmationRepository $repo, EntityManagerInterface $em): Response
  {
    $signalement = $repo->find($id);
    if (!$signalement) {
      throw $this->createNotFoundException('Signalement non trouvé.');
    }

    $signalement->setStatus('rejected');
    $em->flush();

    $this->addFlash('info', 'Signalement rejeté.');

    return $this->redirectToRoute('employee_signalements');
  }
}
