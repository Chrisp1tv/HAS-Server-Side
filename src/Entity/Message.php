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
}