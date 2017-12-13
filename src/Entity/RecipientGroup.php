<?php

namespace App\Entity;

/**
 * RecipientGroup
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class RecipientGroup
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private  $name;

    /**
     * @var Recipient[]
     */
    private $recipients;
}