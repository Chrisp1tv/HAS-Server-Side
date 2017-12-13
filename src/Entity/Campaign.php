<?php

namespace App\Entity;

/**
 * Campaign
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Campaign
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Administrator
     */
    private $sender;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var \DateTime
     */
    private $sendingDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var int
     */
    private $repetitionFrequency;

    /**
     * @var Recipient[]
     */
    private $recipients;

    /**
     * @var RecipientGroup[]
     */
    private $recipientGroups;
}