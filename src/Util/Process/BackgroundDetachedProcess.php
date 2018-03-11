<?php

namespace App\Util\Process;

use Symfony\Component\Process\Process;

/**
 * BackgroundDetachedProcess - This kind of process keeps running even if the script which has launched it ended.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class BackgroundDetachedProcess extends Process
{
    public function __destruct()
    {
    }
}
