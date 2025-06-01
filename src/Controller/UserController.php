<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Vehicles;
use App\Form\VehicleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\UserPreferences;
use App\Form\UserPreferencesType;
use App\Form\UserRolesType;
use App\Repository\UserPreferencesRepository;
use App\Repository\VehiclesRepository;
use Symfony\Bundle\SecurityBundle\Security;

class UserController extends AbstractController
{
    #[Route('/utilisateur/{id}', name: 'user_profile')]
    public function profile(User $user, VehiclesRepository $vehicleRepo, UserPreferencesRepository $prefRepo): Response
    {
        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'vehicles' => $vehicleRepo->findBy(['user' => $user]),
            'preferences' => $prefRepo->findBy(['user' => $user]),
            'isPublic' => true
        ]);
    }

    #[Route('/espace/chauffeur', name: 'app_driver_dashboard')]
    public function driverDashboard(Security $security): Response
    {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // On récupère uniquement les trajets où l'utilisateur est le conducteur
        $carSharings = $user->getCarSharings();

        return $this->render('user/driver_dashboard.html.twig', [
            'carSharings' => $carSharings,
        ]);
    }

    #[Route('/mon-compte/roles', name: 'app_user_roles')]
    #[IsGranted('ROLE_USER')]
    public function chooseRoles(Request $request, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserRolesType::class, [
            'roles' => array_filter($user->getRoles(), fn($r) => in_array($r, ['ROLE_CHAUFFEUR', 'ROLE_PASSAGER']))
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $baseRoles = ['ROLE_USER']; // toujours présent
            $selectedRoles = $data['roles'] ?? [];

            $user->setRoles(array_unique(array_merge($baseRoles, $selectedRoles)));
            $user->setUpdatedAt(new \DateTime());

            $em->flush();

            $this->addFlash('success', 'Votre rôle a bien été mis à jour ✅');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/choose_roles.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/espace/utilisateur/vehicule/{id}/supprimer', name: 'app_vehicle_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteVehicle(Vehicles $vehicle, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($vehicle->getUser() !== $user) {
            $this->addFlash('danger', 'Action non autorisée.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $em->remove($vehicle);
        $em->flush();

        $this->addFlash('success', 'Véhicule supprimé avec succès 🗑️');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/espace/utilisateur', name: 'app_user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'preferences' => $user->getUserPreferences(),
            'vehicles' => $user->getVehicles(),
        ]);
    }

    #[Route('/espace/utilisateur/preference/{id}/supprimer', name: 'app_user_preference_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deletePreference(UserPreferences $userPreference, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($userPreference->getUser() !== $user) {
            $this->addFlash('danger', 'Action non autorisée.');
            return $this->redirectToRoute('app_user_dashboard');
        }

        $em->remove($userPreference);
        $em->flush();

        $this->addFlash('success', 'Préférence supprimée avec succès 🗑️');
        return $this->redirectToRoute('app_user_dashboard');
    }

    #[Route('/espace/utilisateur/vehicule/ajouter', name: 'app_vehicle_new')]
    #[IsGranted('ROLE_USER')]
    public function addVehicle(Request $request, EntityManagerInterface $em): Response
    {
        $vehicle = new Vehicles();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicle = $form->getData(); // <-- AJOUT OBLIGATOIRE

            $vehicle->setUser($this->getUser());
            $vehicle->setCreatedAt(new \DateTime());
            $vehicle->setUpdatedAt(new \DateTime());

            $em->persist($vehicle);
            $em->flush();

            $this->addFlash('success', 'Véhicule ajouté avec succès ✅');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/vehicle_form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/espace/utilisateur/preference/ajouter', name: 'app_user_preference_add')]
    #[IsGranted('ROLE_USER')]
    public function addPreference(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $preference = new UserPreferences();
        $preference->setUser($user);

        $form = $this->createForm(UserPreferencesType::class, $preference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($preference);
            $em->flush();

            $this->addFlash('success', 'Préférence ajoutée avec succès ✅');
            return $this->redirectToRoute('app_user_dashboard');
        }

        return $this->render('user/add_preference.html.twig', [
            'form' => $form,
        ]);
    }
}
