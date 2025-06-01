<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SettingsController extends AbstractController
{
    #[Route('/parametres', name: 'app_settings')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render('settings/settings.html.twig');
    }
}
