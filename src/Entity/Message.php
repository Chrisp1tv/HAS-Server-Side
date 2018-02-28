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
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var int
     */
    private $repetitionFrequency;

    /**
     * @var Campaign
     *
     */
    private $campaign;

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
        $this->endDate = null;
        $this->repetitionFrequency = null;
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

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     *
     * @return $this
     */
    public function setEndDate(?\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getRepetitionFrequency()
    {
        return $this->repetitionFrequency;
    }

    /**
     * @param int $repetitionFrequency
     *
     * @return $this
     */
    public function setRepetitionFrequency(int $repetitionFrequency)
    {
        $this->repetitionFrequency = $repetitionFrequency;

        return $this;
    }

    /**
     * @return Campaign
     */
    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign $campaign
     *
     * @return $this
     */
    public function setCampaign(Campaign $campaign)
    {
        $this->campaign = $campaign;

        return $this;
    }
}