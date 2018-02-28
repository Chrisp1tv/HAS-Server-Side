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
     *
     * The date the campaign was sent.
     */
    private $effectiveSendingDate;

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
        $message->setCampaign($this);

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
    public function getEffectiveSendingDate()
    {
        return $this->effectiveSendingDate;
    }

    /**
     * @return $this
     */
    public function makeSent()
    {
        $this->effectiveSendingDate = new \DateTime();

        return $this;
    }

    /**
     * @return bool
     */
    public function isSent()
    {
        return null != $this->effectiveSendingDate;
    }

    public function shouldBeSent()
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

    public function isModifiable()
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
    public function hasReceived(Recipient $recipient)
    {
        $this->receivedBy->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return bool
     */
    public function hasBeenReceivedBy(Recipient $recipient)
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
    public function hasSeen(Recipient $recipient)
    {
        $this->seenBy->add($recipient);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return bool
     */
    public function hasBeenSeenBy(Recipient $recipient)
    {
        return $this->seenBy->contains($recipient);
    }

    /**
     * @param bool $unique
     *
     * @return Recipient[]
     */
    public function getAllRecipients($unique = false)
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
            } else if (!$seen) {
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

    public function getConsultationStatisticsForGroup(RecipientGroup $recipientGroup = null)
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
            } else if (!$seen) {
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