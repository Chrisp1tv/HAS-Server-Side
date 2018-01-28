<?php

namespace App\Entity;

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
     * @var string
     */
    private $linkingIdentifier;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?? $this->getLinkingIdentifier();
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
     * @return string
     */
    public function getLinkingIdentifier()
    {
        return $this->linkingIdentifier;
    }

    /**
     * @param string $linkingIdentifier
     *
     * @return Recipient
     */
    public function setLinkingIdentifier($linkingIdentifier)
    {
        $this->linkingIdentifier = $linkingIdentifier;

        return $this;
    }
}