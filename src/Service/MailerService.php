<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\CarSharings;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
  private MailerInterface $mailer;
  private Environment $twig;

  public function __construct(MailerInterface $mailer, Environment $twig)
  {
    $this->mailer = $mailer;
    $this->twig = $twig;
  }

  public function sendTripConfirmation(User $user, CarSharings $carSharing, string $token): void
  {
    $email = (new Email())
      ->from('no-reply@ecoride.fr')
      ->to($user->getEmail())
      ->subject('🚗 Merci de confirmer votre trajet EcoRide')
      ->html(
        $this->twig->render('emails/trip_confirmation.html.twig', [
          'user' => $user,
          'carSharing' => $carSharing,
          'token' => $token,
        ])
      );

    $this->mailer->send($email);
  }
}
