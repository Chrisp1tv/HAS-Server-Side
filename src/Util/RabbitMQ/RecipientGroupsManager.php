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

    /**
     * @var Names
     */
    private $names;

    public function __construct(RabbitMQ $rabbitMQ, Names $names)
    {
        $this->rabbitMQ = $rabbitMQ;
        $this->names = $names;
    }

    public function updateRecipientGroupBindings(RecipientGroup $recipientGroup, array $oldGroupRecipients, array $newGroupRecipients)
    {
        $addedRecipients = array_diff($newGroupRecipients, $oldGroupRecipients);
        $removedRecipients = array_diff($oldGroupRecipients, $newGroupRecipients);

        foreach ($addedRecipients as $addedRecipient) {
            $this->rabbitMQ->getChannel()->queue_bind($this->names->getRecipientQueueName($addedRecipient), $this->names->getGroupCampaignsExchangeName(), $this->names->getGroupBindKeyName($recipientGroup));
        }

        foreach ($removedRecipients as $removedRecipient) {
            $this->rabbitMQ->getChannel()->queue_unbind($this->names->getRecipientQueueName($removedRecipient), $this->names->getGroupCampaignsExchangeName(), $this->names->getGroupBindKeyName($recipientGroup));
        }
    }
}