<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestMailerController extends AbstractController
{
  #[Route('/test-mail', name: 'test_mail')]
  public function testMail(MailerInterface $mailer): Response
  {
    $email = (new Email())
      ->from('no-reply@ecoride.fr')
      ->to('mathisfer243@gmail.com')
      ->subject('Test envoi mail Symfony')
      ->text('Ceci est un test d’envoi d’email via Symfony et Mailtrap.');

    try {
      $mailer->send($email);
      $this->addFlash('success', 'Email envoyé avec succès !');
    } catch (\Exception $e) {
      $this->addFlash('danger', 'Erreur lors de l’envoi de l’email : ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_home');
  }
}
