<?php

namespace App\Command;

use App\Util\RabbitMQ\RabbitMQ;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Setup
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Setup extends Command
{
    public static $name = "has:setup";

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RabbitMQ
     */
    protected $rabbitMQ;

    public function __construct(EntityManagerInterface $entityManager, RabbitMQ $rabbitMQ)
    {
        $this->entityManager = $entityManager;
        $this->rabbitMQ = $rabbitMQ;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setDescription('Runs the HAS installation.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $nullOutput = new NullOutput();

        $io->title('Installation command of HAS');

        $io->section('Dabatase creation and schema updating');
        $io->text('The command will now create the database storing all the necessaries information, and the database schema.');

        $command = $this->getApplication()->find('doctrine:database:create');
        $error = $command->run(new ArrayInput(array('--if-not-exists' => true)), $nullOutput);

        if ($error) {
            $io->error('The creation of the database failed, please check your configuration, and that you gave the correct rights to the database user.');

            return 1;
        }

        $command = $this->getApplication()->find('doctrine:schema:update');
        $error = $command->run(new ArrayInput(array('--force' => true)), $nullOutput);

        if ($error) {
            $io->error('The updating of the database schema failed, please check your configuration, and that you gave the correct rights to the database user.');

            return 1;
        }

        $io->section('RabbitMQ configuration');
        $io->text('The command will now check the connection to the RabbitMQ server and configure it to work with HAS.');

        if (!$this->rabbitMQConnectionWorks()) {
            $io->error('The installation command couldn\'t connect to the RabbitMQ server, please check your configuration.');

            return 1;
        }

        $error = !$this->rabbitMQ->setUp();

        if ($error) {
            $io->error('The creation of the queues and exchanges on the RabbitMQ server failed, please check your configuration or your RabbitMQ installation.');

            return 1;
        }

        $io->section('Administration Access configuration');
        $io->text('The command will now invite you to create the first user who\'ll be able to connect on the HAS administration website.');
        $io->newLine();

        $command = $this->getApplication()->find('has:create-user');
        $error = $command->run($input, $output);

        if ($error) {
            $io->error('An error happened when you tried to create the user, please retry by using the has:create-user command.');

            return 1;
        }

        $io->section('Cron jobs (or Windows equivalent) creation');
        $io->text('You should now add the cron jobs (or Windows equivalent) necessary to the project.');
        $io->text('You can find a template of the cron file in the installation guide.');

        return 0;
    }

    /**
     * @return bool
     */
    protected function rabbitMQConnectionWorks()
    {
        try {
            $this->rabbitMQ->getChannel();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}