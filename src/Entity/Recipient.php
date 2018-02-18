<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Recipient
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
        return $this->getName() ?? $this->getId();
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
     * @return Recipient
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getGroups(): ArrayCollection
    {
        return $this->groups;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Recipient
     */
    public function addGroup(RecipientGroup $recipientGroup)
    {
        $this->groups->add($recipientGroup);

        return $this;
    }

    /**
     * @param RecipientGroup $recipientGroup
     *
     * @return Recipient
     */
    public function removeGroup(RecipientGroup $recipientGroup)
    {
        $this->groups->removeElement($recipientGroup);

        return $this;
    }
}