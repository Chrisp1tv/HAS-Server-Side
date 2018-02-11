<?php

namespace App\Util;

use App\Entity\Campaign;
use App\Entity\Message;
use App\Entity\Recipient;
use App\Entity\RecipientGroup;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

/**
 * RabbitMQManager
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
abstract class RabbitMQManager
{
    static public function createRecipientQueue(Producer $directCampaignsProducer, string $clientQueuesPrefix, string $directCampaignsExchangeName, Recipient $recipient)
    {
        $newQueueName = self::getRecipientQueueName($clientQueuesPrefix, $recipient);
        $directCampaignsProducer->getChannel()->queue_declare($newQueueName, false, true, false, false);
        $directCampaignsProducer->getChannel()->queue_bind($newQueueName, $directCampaignsExchangeName, $newQueueName);

        return $newQueueName;
    }

    static public function sendCampaign(Producer $directCampaignsProducer, Producer $groupCampaignsProducer, string $clientQueuesPrefix, string $groupExchangeBindsPrefix, Campaign $campaign)
    {
        $recipients = $campaign->getRecipients()->toArray();
        $recipientGroups = $campaign->getRecipientGroups();
        $encodedMessage = self::encodeMessage($campaign->getMessage());

        if (1 < $recipientGroups->count()) {
            /** @var RecipientGroup $recipientGroup */
            foreach ($recipientGroups->getIterator() as $recipientGroup) {
                $recipients = array_merge($recipients, $recipientGroup->getRecipients()->toArray());
            }

            $recipients = array_unique($recipients, SORT_REGULAR);
        } elseif (1 === $recipientGroups->count()) {
            $groupCampaignsProducer->publish($encodedMessage, self::getGroupBindKey($groupExchangeBindsPrefix, $recipientGroups->first()));
            $recipients = array_diff($recipients, $recipientGroups->first()->toArray());
        }

        foreach ($recipients as $recipient) {
            $directCampaignsProducer->publish($encodedMessage, self::getRecipientQueueName($clientQueuesPrefix, $recipient));
        }
    }

    static public function updateRecipientGroupBindings(Producer $groupCampaignsProducer, string $clientQueuesPrefix, string $groupCampaignsExchangeName, string $groupExchangeBindsPrefix, RecipientGroup $recipientGroup, array $oldGroupRecipients, array $newGroupRecipients)
    {
        $AMPQChannel = $groupCampaignsProducer->getChannel();
        $addedRecipients = array_diff($newGroupRecipients, $oldGroupRecipients);
        $removedRecipients = array_diff($oldGroupRecipients, $newGroupRecipients);

        foreach ($addedRecipients as $addedRecipient) {
            $AMPQChannel->queue_bind(self::getRecipientQueueName($clientQueuesPrefix, $addedRecipient), $groupCampaignsExchangeName, self::getGroupBindKey($groupExchangeBindsPrefix, $recipientGroup));
        }

        foreach ($removedRecipients as $removedRecipient) {
            $AMPQChannel->queue_unbind(self::getRecipientQueueName($clientQueuesPrefix, $removedRecipient), $groupCampaignsExchangeName, self::getGroupBindKey($groupExchangeBindsPrefix, $recipientGroup));
        }
    }

    static private function getRecipientQueueName(string $clientQueuesPrefix, Recipient $recipient)
    {
        return $clientQueuesPrefix . $recipient->getId();
    }

    static private function getGroupBindKey(string $groupExchangeBindsPrefix, RecipientGroup $recipientGroup)
    {
        return $groupExchangeBindsPrefix . $recipientGroup->getId();
    }

    static private function encodeMessage(Message $message)
    {
        return json_encode($message);
    }
}