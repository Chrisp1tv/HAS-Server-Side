<?php

namespace App\Entity;

/**
 * ConnectionLogs
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Administrator
     */
    public function getAdministrator(): Administrator
    {
        return $this->administrator;
    }

    /**
     * @param Administrator $administrator
     *
     * @return ConnectionLogs
     */
    public function setAdministrator(Administrator $administrator): ConnectionLogs
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getConnectionDate(): \DateTime
    {
        return $this->connectionDate;
    }

    /**
     * @param \DateTime $connectionDate
     *
     * @return ConnectionLogs
     */
    public function setConnectionDate(\DateTime $connectionDate): ConnectionLogs
    {
        $this->connectionDate = $connectionDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getIPConnection(): string
    {
        return $this->IPConnection;
    }

    /**
     * @param string $IPConnection
     *
     * @return ConnectionLogs
     */
    public function setIPConnection(string $IPConnection): ConnectionLogs
    {
        $this->IPConnection = $IPConnection;

        return $this;
    }
}