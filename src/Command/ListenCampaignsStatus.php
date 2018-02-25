<?php

namespace App\Command;

use App\Util\RabbitMQManager;
use Doctrine\ORM\EntityManagerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ListenCampaignsStatus
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class ListenCampaignsStatus extends Command
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var RabbitMQManager
     */
    protected $RabbitMQManager;

    public function __construct(EntityManagerInterface $entityManager, RabbitMQManager $RabbitMQManager)
    {
        $this->entityManager = $entityManager;
        $this->RabbitMQManager = $RabbitMQManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('has:listen-campaigns-status')
            ->setDescription('Listens the campaigns status, to know which ones have been received, and red.')
            ->setHidden(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->RabbitMQManager->listenCampaignsStatus(array($this, 'onStatusUpdate'));
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
                return;
        }

        $this->entityManager->flush();
        // TODO: Send ack in all cases
        $this->RabbitMQManager->confirmMessageStatusProcessing($AMQPMessage);
    }
}