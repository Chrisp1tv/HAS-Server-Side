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
}