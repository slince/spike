<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Common\Logger;

use Monolog\Formatter\LineFormatter;
use React\EventLoop\LoopInterface;

class FileHandler extends NonBlockStreamHandler
{
    public function __construct(LoopInterface $loop, $file, $level)
    {
        parent::__construct($loop, $file, $level, true, null, false);
        $this->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
    }
}