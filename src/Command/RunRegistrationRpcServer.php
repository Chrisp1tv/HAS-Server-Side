<?php

namespace App\Command;

use App\EventListener\NewRecipientListener;
use App\Util\RabbitMQ\RecipientsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RunRegistrationRpcServer
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RunRegistrationRpcServer extends Command
{
    public static $name = "has:run-registration-rpc-server";

    use LockableTrait;

    /**
     * @var NewRecipientListener
     */
    protected $newRecipientListener;

    /**
     * @var RecipientsManager
     */
    protected $recipientManagers;

    public function __construct(NewRecipientListener $newRecipientListener, RecipientsManager $recipientsManager)
    {
        $this->newRecipientListener = $newRecipientListener;
        $this->recipientManagers = $recipientsManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::$name)
            ->setDescription('Runs the registration RPC server, and waits for client to register.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln("The command running the registration RPC server is already processing.");

            return;
        }

        try {
            $this->recipientManagers->startRegistrationRpcServer(array($this->newRecipientListener, 'execute'));
        } finally {
            $this->release();
        }
    }
}