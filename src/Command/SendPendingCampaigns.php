<?php

namespace App\Command;

use App\Entity\Campaign;
use App\Util\RabbitMQ\CampaignsManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SendPendingCampaigns - This command sends the campaigns which are ready to be sent.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class SendPendingCampaigns extends Command
{
    use LockableTrait;

    const name = "has:send-pending-campaigns";

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var CampaignsManager
     */
    protected $campaignsManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param CampaignsManager       $campaignsManager
     */
    public function __construct(EntityManagerInterface $entityManager, CampaignsManager $campaignsManager)
    {
        $this->entityManager = $entityManager;
        $this->campaignsManager = $campaignsManager;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(self::name)
            ->setDescription('Sends the pending campaigns, that is to say the campaigns that still needs to be sent to recipients.')
            ->setHidden(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
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
            $this->release();
        }
    }
}
