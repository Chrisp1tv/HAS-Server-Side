<?php

namespace App\Command;

use App\EventListener\NewRecipientListener;
use App\Util\RabbitMQ\RecipientsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RunRegistrationRpcServer - This command runs the RPC registration server, allowing new recipients to register to the database and
 * be able to receive messages from HAS.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RunRegistrationRpcServer extends Command
{
    use LockableTrait;

    const name = "has:run-registration-rpc-server";

    /**
     * @var NewRecipientListener
     */
    protected $newRecipientListener;

    /**
     * @var RecipientsManager
     */
    protected $recipientManagers;

    /**
     * @param NewRecipientListener $newRecipientListener
     * @param RecipientsManager    $recipientsManager
     */
    public function __construct(NewRecipientListener $newRecipientListener, RecipientsManager $recipientsManager)
    {
        $this->newRecipientListener = $newRecipientListener;
        $this->recipientManagers = $recipientsManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::name)
            ->setDescription('Runs the registration RPC server, and waits for client to register.')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
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
