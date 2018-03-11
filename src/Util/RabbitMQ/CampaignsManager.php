<?php

namespace App\Util\RabbitMQ;

use App\Entity\Campaign;
use App\Entity\RecipientGroup;
use JMS\Serializer\SerializerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * CampaignsManager
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class CampaignsManager
{
    /**
     * @var RabbitMQ
     */
    public $rabbitMQ;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param RabbitMQ            $rabbitMQ
     * @param SerializerInterface $serializer
     */
    public function __construct(RabbitMQ $rabbitMQ, SerializerInterface $serializer)
    {
        $this->rabbitMQ = $rabbitMQ;
        $this->serializer = $serializer;
    }

    /**
     * @param Campaign $campaign The campaign to be sent
     */
    public function sendCampaign(Campaign $campaign)
    {
        $recipients = $campaign->getRecipients()->toArray();
        $recipientGroups = $campaign->getRecipientGroups();
        $AMQPMessage = new AMQPMessage($this->serializer->serialize($campaign->getMessage(), 'json'));

        if (1 < $recipientGroups->count()) {
            /** @var RecipientGroup $recipientGroup */
            foreach ($recipientGroups->getIterator() as $recipientGroup) {
                $recipients = array_merge($recipients, $recipientGroup->getRecipients()->toArray());
            }

            $recipients = array_unique($recipients, SORT_REGULAR);
        } elseif (1 === $recipientGroups->count()) {
            $this->rabbitMQ->getChannel()->basic_publish($AMQPMessage, $this->rabbitMQ->getNames()->getGroupCampaignsExchangeName(), $this->rabbitMQ->getNames()->getGroupBindKeyName($recipientGroups->first()));
            $recipients = array_diff($recipients, $recipientGroups->first()->getRecipients()->toArray());
        }

        foreach ($recipients as $recipient) {
            $this->rabbitMQ->getChannel()->basic_publish($AMQPMessage, $this->rabbitMQ->getNames()->getDirectCampaignsExchangeName(), $this->rabbitMQ->getNames()->getRecipientQueueName($recipient));
        }
    }

    /**
     * @param callable $onRequestAction
     */
    public function listenCampaignsStatus(callable $onRequestAction)
    {
        $this->rabbitMQ->listenQueue($this->rabbitMQ->getNames()->getCampaignsStatusQueueName(), $onRequestAction);
        $this->rabbitMQ->wait();
    }
}
