<?php

namespace App\Entity;

/**
 * Administrator
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Administrator
{
    /**
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}