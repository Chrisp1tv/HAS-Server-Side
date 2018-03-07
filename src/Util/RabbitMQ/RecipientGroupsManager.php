<?php

namespace App\Util\RabbitMQ;

use App\Entity\RecipientGroup;

/**
 * RecipientGroupsManager
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroupsManager
{
    /**
     * @var RabbitMQ
     */
    public $rabbitMQ;

    public function __construct(RabbitMQ $rabbitMQ)
    {
        $this->rabbitMQ = $rabbitMQ;
    }

    public function updateRecipientGroupBindings(RecipientGroup $recipientGroup, array $oldGroupRecipients, array $newGroupRecipients)
    {
        $addedRecipients = array_diff($newGroupRecipients, $oldGroupRecipients);
        $removedRecipients = array_diff($oldGroupRecipients, $newGroupRecipients);

        foreach ($addedRecipients as $addedRecipient) {
            $this->rabbitMQ->getChannel()->queue_bind($this->rabbitMQ->getNames()->getRecipientQueueName($addedRecipient), $this->rabbitMQ->getNames()->getGroupCampaignsExchangeName(), $this->rabbitMQ->getNames()->getGroupBindKeyName($recipientGroup));
        }

        foreach ($removedRecipients as $removedRecipient) {
            $this->rabbitMQ->getChannel()->queue_unbind($this->rabbitMQ->getNames()->getRecipientQueueName($removedRecipient), $this->rabbitMQ->getNames()->getGroupCampaignsExchangeName(), $this->rabbitMQ->getNames()->getGroupBindKeyName($recipientGroup));
        }
    }
}