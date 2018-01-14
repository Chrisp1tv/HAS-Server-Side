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
     * @return Recipient
     */
    public function setName(string $name): Recipient
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkingIdentifier(): string
    {
        return $this->linkingIdentifier;
    }

    /**
     * @param string $linkingIdentifier
     *
     * @return Recipient
     */
    public function setLinkingIdentifier(string $linkingIdentifier): Recipient
    {
        $this->linkingIdentifier = $linkingIdentifier;

        return $this;
    }
}