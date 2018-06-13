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
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandler extends NonBlockStreamHandler
{
    public function __construct(LoopInterface $loop, OutputInterface $output, $level)
    {
        parent::__construct($loop, $output->getStream(), $level);
        $this->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
    }
}