<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // Set plain password for validation
            $user->setPlainPassword($plainPassword);

            // Encode and store password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Set default values
            $user->setCredit(20);
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            // Rôle par défaut
            $defaultRole = $entityManager->getRepository(\App\Entity\Roles::class)->findOneBy(['name' => 'ROLE_USER']);

            if (!$defaultRole) {
                throw new \Exception('Le rôle ROLE_USER est introuvable en base.');
            }

            $user->setRole($defaultRole);

            $entityManager->persist($user);
            $entityManager->flush();

            // Log in user and redirect
            $security->login($user, 'form_login', 'main');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
