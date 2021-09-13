<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCreateCommand extends Command
{
    protected static $defaultName = 'app:user:create';
    protected static $defaultDescription = '';

    private EntityManagerInterface $em;

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->em = $em;
        $this->userPasswordHasher = $userPasswordHasher;

        parent::__construct();
    }


    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->em->getRepository(User::class);

        $helper = $this->getHelper('question');

        $output->writeln('');

        $username = '';
        $question = new Question('Please enter username: ', $username);


        while (!$username) {
            $username = $helper->ask($input, $output, $question);

            if ($repository->findOneBy(['username' => $username])) {
                $output->writeln(sprintf('<error>Username %s already exists. Please choose another one</error>', $username));
                $username = '';
            }
        }

        $plainPassword = '';
        $question = new Question('Please enter password: ', $plainPassword);
        $question->setHidden(true);

        while (!$plainPassword) {
            $plainPassword = $helper->ask($input, $output, $question);
            if (strlen($plainPassword < 5)) {
                $output->writeln('<error>Password too short. Provide at least 5 characters.</error>');
            }
        }

        $roles = ['ROLE_USER'];
        $question = new ConfirmationQuestion('Grant User Admin role? ', false);

        if ($helper->ask($input, $output, $question)) {
            $roles[] = User::ROLE_ADMIN;
        }

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
        $user->setRoles($roles);

        $this->em->persist($user);
        $this->em->flush();

        $output->writeln('');
        $output->writeln('User successfully created.');
        $output->writeln('');

        return Command::SUCCESS;
    }
}
