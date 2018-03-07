<?php

namespace App\Command;

use App\Util\RabbitMQ\CampaignsManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ListenCampaignsStatus
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class ListenCampaignsStatus extends Command
{
    public static $name = "has:listen-campaigns-status";

    use LockableTrait;

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
            ->setName(self::$name)
            ->setDescription('Listens the campaigns status, to know which ones have been received, and red.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('The command listening the campaigns status is already running.');

            return;
        }

        try {
            $this->campaignsManager->listenCampaignsStatus(array($this, 'onStatusUpdate'));
        } finally {
            $this->release();
        }
    }

    public function onStatusUpdate(AMQPMessage $AMQPMessage)
    {
        $campaignStatus = json_decode($AMQPMessage->getBody());

        if (!property_exists($campaignStatus, 'recipientId')
            or !property_exists($campaignStatus, 'campaignId')
            or !property_exists($campaignStatus, 'status')
            or !$campaign = $this->entityManager->getRepository('App\Entity\Campaign')->find($campaignStatus->campaignId)
            or !$recipient = $this->entityManager->getRepository('App\Entity\Recipient')->find($campaignStatus->recipientId)
        ) {
            $this->campaignsManager->rabbitMQ->sendAck($AMQPMessage);

            return;
        }

        switch ($campaignStatus->status) {
            case 'received':
                $campaign->hasReceived($recipient);
                break;
            case 'seen':
                $campaign->hasSeen($recipient);
                break;
            default:
                $this->campaignsManager->rabbitMQ->sendAck($AMQPMessage);

                return;
        }

        $this->entityManager->flush();
        $this->campaignsManager->rabbitMQ->sendAck($AMQPMessage);
    }
}