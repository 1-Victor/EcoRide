<?php

// src/Controller/Admin/AdminController.php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Suspensions;
use App\Form\CreateEmployeeType;
use App\Form\SuspensionType;
use App\Repository\CarSharingsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/utilisateurs', name: 'admin_users')]
    public function manageUsers(UserRepository $repo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    #[Route('/utilisateurs/{id}/add-employe', name: 'admin_add_employe')]
    public function addEmploye(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_EMPLOYE', $roles)) {
            $roles[] = 'ROLE_EMPLOYE';
            $user->setRoles($roles);
            $em->flush();
            $this->addFlash('success', 'Rôle EMPLOYE ajouté.');
        }

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateurs/{id}/remove-employe', name: 'admin_remove_employe')]
    public function removeEmploye(User $user, EntityManagerInterface $em): Response
    {
        $roles = array_filter($user->getRoles(), fn($role) => $role !== 'ROLE_EMPLOYE');
        $user->setRoles($roles);
        $em->flush();
        $this->addFlash('success', 'Rôle EMPLOYE retiré.');

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateurs/{id}/suspend', name: 'admin_suspend_user')]
    public function suspendUser(
        Request $request,
        User $user,
        EntityManagerInterface $em
    ): Response {
        $suspension = new Suspensions();
        $suspension->setUser($user);
        $suspension->setAdmin($this->getUser());
        $suspension->setCreatedAt(new \DateTime());

        $form = $this->createForm(SuspensionType::class, $suspension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($suspension);
            $em->flush();

            $this->addFlash('warning', 'Utilisateur suspendu.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/suspend_user.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/utilisateurs/{id}/unsuspend', name: 'admin_unsuspend_user')]
    public function unsuspendUser(User $user, EntityManagerInterface $em): Response
    {
        foreach ($user->getSuspensions() as $suspension) {
            $em->remove($suspension);
        }
        $em->flush();

        $this->addFlash('success', 'Utilisateur réactivé.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/stats', name: 'admin_stats')]
    public function stats(CarSharingsRepository $repo): Response
    {
        $stats = $repo->countCarSharingsByDay();
        $totalCarSharings = $repo->getTotalCarSharingsCount();
        $totalCredits = $totalCarSharings * 2;

        $labels = [];
        $nbTrajets = [];
        $nbCredits = [];

        foreach ($stats as $entry) {
            $labels[] = $entry['jour'];
            $nbTrajets[] = $entry['nb'];
            $nbCredits[] = $entry['nb'] * 2;
        }

        return $this->render('admin/stats.html.twig', [
            'labels' => json_encode($labels),
            'nbTrajets' => json_encode($nbTrajets),
            'nbCredits' => json_encode($nbCredits),
            'totalCredits' => $totalCredits,
        ]);
    }

    #[Route('/credits', name: 'admin_credits')]
    public function credits(CarSharingsRepository $repo): Response
    {
        $gainData = $repo->countCarSharingsByDay();
        $gains = [];
        $totalGained = 0;
        foreach ($gainData as $entry) {
            $gains[] = [
                'date' => $entry['jour'],
                'count' => $entry['nb'],
                'credits' => $entry['nb'] * 2
            ];
            $totalGained += $entry['nb'] * 2;
        }

        $lossData = $repo->countCancelledCarSharingsByDay();
        $losses = [];
        $totalLost = 0;
        foreach ($lossData as $entry) {
            $losses[] = [
                'date' => $entry['jour'],
                'count' => $entry['nb'],
                'credits' => $entry['nb'] * 2
            ];
            $totalLost += $entry['nb'] * 2;
        }

        return $this->render('admin/total_credits.html.twig', [
            'gains' => $gains,
            'losses' => $losses,
            'totalGained' => $totalGained,
            'totalLost' => $totalLost,
        ]);
    }

    #[Route('/employes/create', name: 'admin_create_employee')]
    public function createEmployePage(
        Request $request,
        UserRepository $userRepo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $user = new User();
        $form = $this->createForm(CreateEmployeeType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_EMPLOYE']);
            $user->setCredit(0);
            $user->setCreatedAt(new \DateTime());
            $user->setUpdatedAt(new \DateTime());
            $user->setPassword($hasher->hashPassword($user, $user->getPlainPassword()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Nouvel employé créé.');
            return $this->redirectToRoute('admin_create_employee');
        }

        return $this->render('admin/create_employee.html.twig', [
            'form' => $form->createView(),
            'users' => $userRepo->findAll(),
        ]);
    }
}
