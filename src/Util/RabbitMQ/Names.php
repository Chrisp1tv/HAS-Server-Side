<?php

namespace App\Util\RabbitMQ;

use App\Entity\Recipient;
use App\Entity\RecipientGroup;

/**
 * Names
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Names
{
    /**
     * @var string
     */
    private $directCampaignsExchangeName;

    /**
     * @var string
     */
    private $groupCampaignsExchangeName;

    /**
     * @var string
     */
    private $recipientRegistrationExchangeName;

    /**
     * @var string
     */
    private $recipientRegistrationQueueName;

    /**
     * @var string
     */
    private $campaignsStatusExchangeName;

    /**
     * @var string
     */
    private $campaignsStatusQueueName;

    /**
     * @var string
     */
    private $clientQueuesPrefix;

    /**
     * @var string
     */
    private $groupExchangeBindsPrefix;

    /**
     * @param $directCampaignsExchangeName
     * @param $groupCampaignsExchangeName
     * @param $recipientRegistrationExchangeName
     * @param $recipientRegistrationQueueName
     * @param $campaignsStatusExchangeName
     * @param $campaignsStatusQueueName
     * @param $clientQueuesPrefix
     * @param $groupExchangeBindsPrefix
     */
    public function __construct($directCampaignsExchangeName, $groupCampaignsExchangeName, $recipientRegistrationExchangeName, $recipientRegistrationQueueName, $campaignsStatusExchangeName, $campaignsStatusQueueName, $clientQueuesPrefix, $groupExchangeBindsPrefix)
    {
        $this->directCampaignsExchangeName = $directCampaignsExchangeName;
        $this->groupCampaignsExchangeName = $groupCampaignsExchangeName;
        $this->recipientRegistrationExchangeName = $recipientRegistrationExchangeName;
        $this->recipientRegistrationQueueName = $recipientRegistrationQueueName;
        $this->campaignsStatusExchangeName = $campaignsStatusExchangeName;
        $this->campaignsStatusQueueName = $campaignsStatusQueueName;
        $this->clientQueuesPrefix = $clientQueuesPrefix;
        $this->groupExchangeBindsPrefix = $groupExchangeBindsPrefix;
    }

    public function getRecipientQueueName(Recipient $recipient)
    {
        return $this->getClientQueuesPrefix() . $recipient->getId();
    }

    public function getGroupBindKeyName(RecipientGroup $recipientGroup)
    {
        return $this->getGroupExchangeBindsPrefix() . $recipientGroup->getId();
    }

    /**
     * @return string
     */
    public function getDirectCampaignsExchangeName(): string
    {
        return $this->directCampaignsExchangeName;
    }

    /**
     * @return string
     */
    public function getGroupCampaignsExchangeName(): string
    {
        return $this->groupCampaignsExchangeName;
    }

    /**
     * @return string
     */
    public function getRecipientRegistrationExchangeName(): string
    {
        return $this->recipientRegistrationExchangeName;
    }

    /**
     * @return string
     */
    public function getRecipientRegistrationQueueName(): string
    {
        return $this->recipientRegistrationQueueName;
    }

    /**
     * @return string
     */
    public function getCampaignsStatusExchangeName(): string
    {
        return $this->campaignsStatusExchangeName;
    }

    /**
     * @return string
     */
    public function getCampaignsStatusQueueName(): string
    {
        return $this->campaignsStatusQueueName;
    }

    /**
     * @return string
     */
    public function getClientQueuesPrefix(): string
    {
        return $this->clientQueuesPrefix;
    }

    /**
     * @return string
     */
    public function getGroupExchangeBindsPrefix(): string
    {
        return $this->groupExchangeBindsPrefix;
    }
}