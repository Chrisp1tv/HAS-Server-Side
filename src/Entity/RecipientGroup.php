<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * RecipientGroup - Represents a group of recipients, which can be used to send messages to many recipients at a time.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroup
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
     * @var ArrayCollection
     */
    private $recipients;

    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * @return RecipientGroup
     */
    public function setName(?string $name): RecipientGroup
    {
        $this->name = $name;

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
     * @return RecipientGroup
     */
    public function addRecipient(Recipient $recipient): RecipientGroup
    {
        $this->recipients->add($recipient);
        $recipient->addGroup($this);

        return $this;
    }

    /**
     * @param Recipient $recipient
     *
     * @return RecipientGroup
     */
    public function removeRecipient(Recipient $recipient): RecipientGroup
    {
        $this->recipients->removeElement($recipient);
        $recipient->removeGroup($this);

        return $this;
    }
}
