<?php

namespace App\Entity;

/**
 * Message
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Message
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $color;

    /**
     * @var bool
     */
    private $bold;

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
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Message
     */
    public function setContent(string $content): Message
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Message
     */
    public function setColor(string $color): Message
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBold(): bool
    {
        return $this->bold;
    }

    /**
     * @param boolean $bold
     *
     * @return Message
     */
    public function setBold(bool $bold): Message
    {
        $this->bold = $bold;

        return $this;
    }
}