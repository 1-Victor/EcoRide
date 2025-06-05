<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Créer un compte administrateur avec tous les champs requis.',
)]
class CreateAdminCommand extends Command
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Questions
        $email = $helper->ask($input, $output, new Question("Email : "));
        $username = $helper->ask($input, $output, new Question("Nom d'utilisateur (username) : "));
        $firstname = $helper->ask($input, $output, new Question("Prénom : "));
        $lastname = $helper->ask($input, $output, new Question("Nom : "));
        $phone = $helper->ask($input, $output, new Question("Téléphone : "));
        $passwordQuestion = new Question("Mot de passe : ");
        $passwordQuestion->setHidden(true);
        $plainPassword = $helper->ask($input, $output, $passwordQuestion);

        // Création de l'utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);
        $user->setPhone($phone);
        $user->setCredit(20);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());

        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln("✅ Compte administrateur créé avec succès.");

        return Command::SUCCESS;
    }
}
