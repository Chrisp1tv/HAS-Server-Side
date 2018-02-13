<?php

namespace App\Command;

use App\Entity\Campaign;
use App\Util\RabbitMQManager;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
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
     * @var Producer
     */
    protected $directCampaignsProducer;

    /**
     * @var Producer
     */
    protected $groupCampaignsProducer;

    /**
     * @var string
     */
    protected $clientQueuesPrefix;

    /**
     * @var string
     */
    protected $groupExchangeBindsPrefix;

    public function __construct(EntityManagerInterface $entityManager, Producer $directCampaignsProducer, Producer $groupCampaignsProducer, string $clientQueuesPrefix, string $groupExchangeBindsPrefix)
    {
        $this->entityManager = $entityManager;
        $this->directCampaignsProducer = $directCampaignsProducer;
        $this->groupCampaignsProducer = $groupCampaignsProducer;
        $this->clientQueuesPrefix = $clientQueuesPrefix;
        $this->groupExchangeBindsPrefix = $groupExchangeBindsPrefix;

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
                    RabbitMQManager::sendCampaign($this->directCampaignsProducer, $this->groupCampaignsProducer, $this->clientQueuesPrefix, $this->groupExchangeBindsPrefix, $campaign);
                    $campaign->makeSent();
                }

                $this->entityManager->flush();
            }
        } finally {
            $lock->release();
        }
    }
}