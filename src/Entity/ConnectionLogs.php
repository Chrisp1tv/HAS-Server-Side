<?php

namespace App\Entity;

/**
 * ConnectionLogs - Represents a administrator connection to HAS.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class ConnectionLogs
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Administrator
     */
    private $administrator;

    /**
     * @var \DateTime
     */
    private $connectionDate;

    /**
     * @var string
     */
    private $IPConnection;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Administrator|null
     */
    public function getAdministrator(): ?Administrator
    {
        return $this->administrator;
    }

    /**
     * @param Administrator|null $administrator
     *
     * @return ConnectionLogs
     */
    public function setAdministrator(?Administrator $administrator): ConnectionLogs
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getConnectionDate(): ?\DateTime
    {
        return $this->connectionDate;
    }

    /**
     * @param \DateTime|null $connectionDate
     *
     * @return ConnectionLogs
     */
    public function setConnectionDate(?\DateTime $connectionDate): ConnectionLogs
    {
        $this->connectionDate = $connectionDate;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getIPConnection(): ?string
    {
        return $this->IPConnection;
    }

    /**
     * @param null|string $IPConnection
     *
     * @return $this
     */
    public function setIPConnection(?string $IPConnection)
    {
        $this->IPConnection = $IPConnection;

        return $this;
    }
}
