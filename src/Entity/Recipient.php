<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Recipient - Represents a recipient, that is to say a computer allowed to receive messages from HAS.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Recipient
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
    private $groups;

    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)($this->getName() ?? $this->getId());
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
     * @return Recipient
     */
    public function setName(?string $name): Recipient
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Recipient
     */
    public function addGroup(RecipientGroup $recipientGroup): Recipient
    {
        $this->groups->add($recipientGroup);

        return $this;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Recipient
     */
    public function removeGroup(RecipientGroup $recipientGroup): Recipient
    {
        $this->groups->removeElement($recipientGroup);

        return $this;
    }
}
