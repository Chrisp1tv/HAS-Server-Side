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
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }

    public function __clone()
    {
        $this->id = null;
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
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     *
     * @return Message
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBold()
    {
        return $this->bold;
    }

    /**
     * @param boolean $bold
     *
     * @return Message
     */
    public function setBold($bold)
    {
        $this->bold = $bold;

        return $this;
    }
}