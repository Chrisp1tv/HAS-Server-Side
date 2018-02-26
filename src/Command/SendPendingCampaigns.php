<?php

namespace App\Command;

use App\Entity\Campaign;
use App\Util\RabbitMQ\CampaignsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Lock\Store\FlockStore;

/**
 * SendPendingCampaigns
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class SendPendingCampaigns extends Command
{
    protected static $LOCK_NAME = 'CampaignSenderLock';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CampaignsManager
     */
    protected $campaignsManager;

    public function __construct(EntityManagerInterface $entityManager, CampaignsManager $campaignsManager)
    {
        $this->entityManager = $entityManager;
        $this->campaignsManager = $campaignsManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('has:send-pending-campaigns')
            ->setDescription('Sends the pending campaigns, that is to say the campaigns that still needs to be sent to recipients.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $store = new FlockStore(sys_get_temp_dir());
        $factory = new Factory($store);
        $lock = $factory->createLock(static::$LOCK_NAME);

        if (!$lock->acquire()) {
            $output->writeln("The command sending campaigns is already processing.");

            return;
        }

        try {
            if ($unsentCampaigns = $this->entityManager->getRepository('App\Entity\Campaign')->findUnsent()) {
                /** @var Campaign $campaign */
                foreach ($unsentCampaigns as $campaign) {
                    $this->campaignsManager->sendCampaign($campaign);
                    $campaign->makeSent();
                }

                $this->entityManager->flush();
            }
        } finally {
            $lock->release();
        }
    }
}