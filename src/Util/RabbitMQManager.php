<?php

namespace App\Util;

use App\Entity\Campaign;
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
    static public function createRecipientQueue(Producer $directCampaignsProducer, string $clientQueuesPrefix, Recipient $recipient)
    {
        $newQueueName = self::getRecipientQueueName($clientQueuesPrefix, $recipient);
        $directCampaignsProducer->getChannel()->queue_declare($newQueueName, false, true, false, false);

        // TODO: bind queue to the direct campaign exchange

        return $newQueueName;
    }

    static public function sendCampaign(Campaign $campaign, Producer $directCampaignsProducer, Producer $groupCampaignsProducer)
    {
        // We can't really send a message to multiple groups at the same time without risking to send same message multiple times to some recipients,
        // so let's find a workaround
        // TODO
        if ($campaign->getRecipientGroups()->count() > 1) {

        } else {
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
}