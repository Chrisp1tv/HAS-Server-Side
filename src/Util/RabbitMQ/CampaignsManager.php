<?php

namespace App\Util\RabbitMQ;

use App\Entity\Campaign;
use App\Entity\RecipientGroup;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

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
     * @var Names
     */
    private $names;

    public function __construct(RabbitMQ $rabbitMQ, Names $names)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function sendCampaign(Campaign $campaign)
    {
        $serializer = new Serializer(array(new ObjectNormalizer()), array(new JsonEncoder()));
        $recipients = $campaign->getRecipients()->toArray();
        $recipientGroups = $campaign->getRecipientGroups();
        $AMQPMessage = new AMQPMessage($serializer->serialize($campaign->getMessage(), 'json'));

        if (1 < $recipientGroups->count()) {
            /** @var RecipientGroup $recipientGroup */
            foreach ($recipientGroups->getIterator() as $recipientGroup) {
                $recipients = array_merge($recipients, $recipientGroup->getRecipients()->toArray());
            }

            $recipients = array_unique($recipients, SORT_REGULAR);
        } elseif (1 === $recipientGroups->count()) {
            $this->rabbitMQ->getChannel()->basic_publish($AMQPMessage, $this->names->getGroupCampaignsExchangeName(), $this->names->getGroupBindKeyName($recipientGroups->first()));
            $recipients = array_diff($recipients, $recipientGroups->first()->getRecipients()->toArray());
        }

        foreach ($recipients as $recipient) {
            $this->rabbitMQ->getChannel()->basic_publish($AMQPMessage, $this->names->getDirectCampaignsExchangeName(), $this->names->getRecipientQueueName($recipient));
        }
    }

    public function listenCampaignsStatus(callable $onRequestAction)
    {
        $this->rabbitMQ->listenQueue($this->names->getCampaignsStatusQueueName(), $onRequestAction);
        $this->rabbitMQ->wait();
    }
}