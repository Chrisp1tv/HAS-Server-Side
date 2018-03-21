<?php

namespace App\Entity;

/**
 * Message - Represents a message, as sent to recipients.
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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param null|string $content
     *
     * @return Message
     */
    public function setContent(?string $content): Message
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    /**
     * @param \DateTime|null $endDate
     *
     * @return Message
     */
    public function setEndDate(?\DateTime $endDate): Message
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getRepetitionFrequency(): ?int
    {
        return $this->repetitionFrequency;
    }

    /**
     * @param int|null $repetitionFrequency
     *
     * @return Message
     */
    public function setRepetitionFrequency(?int $repetitionFrequency): Message
    {
        $this->repetitionFrequency = $repetitionFrequency;

        return $this;
    }

    /**
     * @return Campaign|null
     */
    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    /**
     * @param Campaign|null $campaign
     *
     * @return Message
     */
    public function setCampaign(?Campaign $campaign): Message
    {
        $this->campaign = $campaign;

        return $this;
    }
}
