<?php

namespace App\Command;

use App\EventListener\NewRecipientListener;
use App\Util\RabbitMQ\RecipientsManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * RunRegistrationRpcServer
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RunRegistrationRpcServer extends Command
{
    protected static $LOCK_NAME = 'RegistrationServerLock';

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
            ->setName('has:run-registration-rpc-server')
            ->setDescription('Runs the registration RPC server, and waits for client to register.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $store = new FlockStore(sys_get_temp_dir());
        $factory = new Factory($store);
        $lock = $factory->createLock(static::$LOCK_NAME);

        if (!$lock->acquire()) {
            $output->writeln("The command running the registration RPC server is already processing.");

            return;
        }

        try {
            $this->recipientManagers->startRegistrationRpcServer(array($this->newRecipientListener, 'execute'));
        } finally {
            $lock->release();
        }
    }
}