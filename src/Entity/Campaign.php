<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @var ArrayCollection
     */
    private $recipients;

    /**
     * @var ArrayCollection
     */
    private $recipientGroups;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->recipientGroups = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    public function __clone()
    {
        $this->id = null;
        $this->message = clone $this->message;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Campaign
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Administrator
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param Administrator $sender
     *
     * @return Campaign
     */
    public function setSender(Administrator $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param Message $message
     *
     * @return Campaign
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendingDate()
    {
        return $this->sendingDate;
    }

    /**
     * @param \DateTime $sendingDate
     *
     * @return Campaign
     */
    public function setSendingDate(\DateTime $sendingDate)
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return Campaign
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getRepetitionFrequency()
    {
        return $this->repetitionFrequency;
    }

    /**
     * @param int $repetitionFrequency
     *
     * @return Campaign
     */
    public function setRepetitionFrequency(int $repetitionFrequency)
    {
        $this->repetitionFrequency = $repetitionFrequency;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function addRecipient(Recipient $recipient)
    {
        $this->recipients->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function removeRecipient(Recipient $recipient)
    {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipientGroups()
    {
        return $this->recipientGroups;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Campaign
     */
    public function addRecipientGroup(RecipientGroup $recipientGroup)
    {
        $this->recipientGroups->add($recipientGroup);

        return $this;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Campaign
     */
    public function removeRecipientGroup(RecipientGroup $recipientGroup)
    {
        $this->recipientGroups->removeElement($recipientGroup);

        return $this;
    }
}