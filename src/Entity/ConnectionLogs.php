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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Administrator
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * @param Administrator $administrator
     *
     * @return ConnectionLogs
     */
    public function setAdministrator(Administrator $administrator)
    {
        $this->administrator = $administrator;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getConnectionDate()
    {
        return $this->connectionDate;
    }

    /**
     * @param \DateTime $connectionDate
     *
     * @return ConnectionLogs
     */
    public function setConnectionDate(\DateTime $connectionDate)
    {
        $this->connectionDate = $connectionDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getIPConnection()
    {
        return $this->IPConnection;
    }

    /**
     * @param string $IPConnection
     *
     * @return ConnectionLogs
     */
    public function setIPConnection($IPConnection)
    {
        $this->IPConnection = $IPConnection;

        return $this;
    }
}