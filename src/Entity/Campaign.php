<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Campaign - Represents a message and its information.
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
     *
     * The date the campaign was sent.
     */
    private $effectiveSendingDate;

    /**
     * @var bool
     */
    private $sendToAllRecipients;

    /**
     * @var ArrayCollection
     */
    private $recipients;

    /**
     * @var ArrayCollection
     */
    private $recipientGroups;

    /**
     * @var ArrayCollection
     */
    private $receivedBy;

    /**
     * @var ArrayCollection
     */
    private $seenBy;

    /**
     * @var Recipient[]
     */
    private $allRecipients;

    /**
     * @var Recipient[]
     */
    private $allUniqueRecipients;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->recipientGroups = new ArrayCollection();
        $this->receivedBy = new ArrayCollection();
        $this->seenBy = new ArrayCollection();
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
        $this->sendingDate = null;
        $this->effectiveSendingDate = null;
        $this->message = clone $this->message;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     *
     * @return Campaign
     */
    public function setName(?string $name): Campaign
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Administrator|null
     */
    public function getSender(): ?Administrator
    {
        return $this->sender;
    }

    /**
     * @param Administrator|null $sender
     *
     * @return Campaign
     */
    public function setSender(?Administrator $sender): Campaign
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return Message|null
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }

    /**
     * @param Message|null $message
     *
     * @return Campaign
     */
    public function setMessage(?Message $message): Campaign
    {
        $this->message = $message;
        $message->setCampaign($this);

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getSendingDate(): ?\DateTime
    {
        return $this->sendingDate;
    }

    /**
     * @param \DateTime|null $sendingDate
     *
     * @return Campaign
     */
    public function setSendingDate(?\DateTime $sendingDate): Campaign
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEffectiveSendingDate(): ?\DateTime
    {
        return $this->effectiveSendingDate;
    }

    /**
     * @return Campaign
     */
    public function makeSent(): Campaign
    {
        $this->effectiveSendingDate = new \DateTime();

        return $this;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return null != $this->effectiveSendingDate;
    }

    /**
     * @return bool True if the campaign should be sent, false otherwise
     */
    public function shouldBeSent(): bool
    {
        return null === $this->effectiveSendingDate and new \DateTime() > $this->sendingDate;
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
    public function addRecipient(Recipient $recipient): Campaign
    {
        $this->recipients->add($recipient);

        return $this;
    }

    /**
     * @param array $recipients
     *
     * @return Campaign
     */
    public function addAllRecipients(array $recipients): Campaign
    {
        $this->recipients = new ArrayCollection(array_merge($this->recipients->toArray(), $recipients));

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function removeRecipient(Recipient $recipient): Campaign
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
    public function addRecipientGroup(RecipientGroup $recipientGroup): Campaign
    {
        $this->recipientGroups->add($recipientGroup);

        return $this;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Campaign
     */
    public function removeRecipientGroup(RecipientGroup $recipientGroup): Campaign
    {
        $this->recipientGroups->removeElement($recipientGroup);

        return $this;
    }

    /**
     * @return bool True if the campaign is allowed to be modified, false otherwise
     */
    public function isModifiable(): bool
    {
        return $this->sendingDate > new \DateTime();
    }

    /**
     * @return ArrayCollection
     */
    public function getReceivedBy()
    {
        return $this->receivedBy;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function hasReceived(Recipient $recipient): Campaign
    {
        $this->receivedBy->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return bool True if the given recipient received the campaign, false otherwise
     */
    public function hasBeenReceivedBy(Recipient $recipient): bool
    {
        return $this->receivedBy->contains($recipient);
    }

    /**
     * @return ArrayCollection
     */
    public function getSeenBy()
    {
        return $this->seenBy;
    }

    /**
     * @param Recipient $recipient
     *
     * @return Campaign
     */
    public function hasSeen(Recipient $recipient): Campaign
    {
        $this->seenBy->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return bool True if the given recipient saw the campaign, false otherwise
     */
    public function hasBeenSeenBy(Recipient $recipient)
    {
        return $this->seenBy->contains($recipient);
    }

    /**
     * @return bool True if the campaign should be sent to all known recipients, false otherwise.
     */
    public function isSendToAllRecipients(): ?bool
    {
        return $this->sendToAllRecipients;
    }

    /**
     * @param bool $sendToAllRecipients
     *
     * @return Campaign
     */
    public function setSendToAllRecipients(bool $sendToAllRecipients): Campaign
    {
        $this->sendToAllRecipients = $sendToAllRecipients;

        return $this;
    }

    /**
     * @param bool $unique
     *
     * @return Recipient[]
     */
    public function getAllRecipients(bool $unique = false)
    {
        if (null === $this->allRecipients or null === $this->allUniqueRecipients) {
            $allRecipients = $this->getRecipients()->toArray();

            /** @var $recipientGroup RecipientGroup */
            foreach ($this->getRecipientGroups()->toArray() as $recipientGroup) {
                $allRecipients = array_merge($allRecipients, $recipientGroup->getRecipients()->toArray());
            }

            $this->allRecipients = $allRecipients;
            $this->allUniqueRecipients = array_unique($allRecipients, SORT_REGULAR);
        }

        return $unique ? $this->allUniqueRecipients : $this->allRecipients;
    }

    /**
     * @return array The constructed data statistics
     */
    public function getGlobalConsultationStatistics()
    {
        $notReceived = 0;
        $receivedOnly = 0;
        $receivedAndRead = 0;

        foreach ($this->getAllRecipients(true) as $recipient) {
            $received = $this->hasBeenReceivedBy($recipient);
            $seen = $received and $this->hasBeenSeenBy($recipient);

            if (!$received) {
                $notReceived++;
                continue;
            } elseif (!$seen) {
                $receivedOnly++;
                continue;
            } else {
                $receivedAndRead++;
            }
        }

        return array(
            'notReceived'     => $notReceived,
            'receivedOnly'    => $receivedOnly,
            'receivedAndSeen' => $receivedAndRead,
        );
    }

    /**
     * @param RecipientGroup|null $recipientGroup
     *
     * @return array The constructed data statistics
     */
    public function getConsultationStatisticsForGroup(?RecipientGroup $recipientGroup)
    {
        $notReceived = 0;
        $receivedOnly = 0;
        $receivedAndRead = 0;

        if (null === $recipientGroup) {
            $recipients = $this->getRecipients()->toArray();
        } else {
            $recipients = $recipientGroup->getRecipients()->toArray();
        }

        foreach ($recipients as $recipient) {
            $received = $this->hasBeenReceivedBy($recipient);
            $seen = $received and $this->hasBeenSeenBy($recipient);

            if (!$received) {
                $notReceived++;
                continue;
            } elseif (!$seen) {
                $receivedOnly++;
                continue;
            } else {
                $receivedAndRead++;
            }
        }

        return array(
            'notReceived'     => $notReceived,
            'receivedOnly'    => $receivedOnly,
            'receivedAndSeen' => $receivedAndRead,
        );
    }
}
