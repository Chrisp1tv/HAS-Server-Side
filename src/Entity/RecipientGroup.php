<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RecipientGroup
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
    private  $name;

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
     * @return RecipientGroup
     */
    public function setName(string $name): RecipientGroup
    {
        $this->name = $name;

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
     * @return RecipientGroup
     */
    public function addRecipient(Recipient $recipient): RecipientGroup
    {
        $this->recipients->add($recipient);

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

        return $this;
    }
}