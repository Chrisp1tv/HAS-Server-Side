<?php

namespace App\Command;

use App\Entity\Administrator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CreateUser
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CreateUser extends Command
{
    public static $name = "has:create-user";

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    protected $userPasswordEncoder;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setDescription('Creates a new user.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $usernameQuestion = new Question('Enter the username of the user: ');
        $passwordQuestion = new Question('Enter the password of the user: ');
        $passwordQuestion->setHidden(true);

        $administrator = new Administrator();

        $administrator
            ->setUsername($helper->ask($input, $output, $usernameQuestion))
            ->setPassword($helper->ask($input, $output, $passwordQuestion))
            ->addRole('ROLE_USER');

        $errors = $this->validator->validate($administrator);
        if (0 < count($errors)) {
            // TODO: Improve errors displaying
            $io->error((string)$errors);

            return 1;
        }

        $administrator->setPassword($this->userPasswordEncoder->encodePassword($administrator, $administrator->getPassword()));

        $this->entityManager->persist($administrator);
        $this->entityManager->flush();

        $io->success("The user named " . $administrator->getUsername() . " has been created.");

        return 0;
    }
}