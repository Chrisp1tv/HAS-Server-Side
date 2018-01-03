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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Campaign
     */
    public function setName(string $name): Campaign
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Administrator
     */
    public function getSender(): Administrator
    {
        return $this->sender;
    }

    /**
     * @param Administrator $sender
     *
     * @return Campaign
     */
    public function setSender(Administrator $sender): Campaign
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @param Message $message
     *
     * @return Campaign
     */
    public function setMessage(Message $message): Campaign
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSendingDate(): \DateTime
    {
        return $this->sendingDate;
    }

    /**
     * @param \DateTime $sendingDate
     *
     * @return Campaign
     */
    public function setSendingDate(\DateTime $sendingDate): Campaign
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime $endDate
     *
     * @return Campaign
     */
    public function setEndDate(\DateTime $endDate): Campaign
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getRepetitionFrequency(): int
    {
        return $this->repetitionFrequency;
    }

    /**
     * @param int $repetitionFrequency
     *
     * @return Campaign
     */
    public function setRepetitionFrequency(int $repetitionFrequency): Campaign
    {
        $this->repetitionFrequency = $repetitionFrequency;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipients(): ArrayCollection
    {
        return $this->recipients;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function addRecipient(Recipient $recipient): Campaign {
        $this->recipients->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function removeRecipient(Recipient $recipient): Campaign {
        $this->recipients->removeElement($recipient);

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipientGroups(): ArrayCollection
    {
        return $this->recipientGroups;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Campaign
     */
    public function addRecipientGroup(RecipientGroup $recipientGroup): Campaign {
        $this->recipientGroups->add($recipientGroup);

        return $this;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Campaign
     */
    public function removeRecipientGroup(RecipientGroup $recipientGroup): Campaign {
        $this->recipientGroups->removeElement($recipientGroup);

        return $this;
    }
}