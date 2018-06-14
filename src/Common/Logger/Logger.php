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

use Monolog\Logger as Monologer;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Logger extends Monologer
{
    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * The log file.
     *
     * @var string
     */
    protected $file;

    /**
     * The log level.
     *
     * @var int|string
     */
    protected $level;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(LoopInterface $loop, $level, $file, OutputInterface $output)
    {
        $this->eventLoop = $loop;
        $this->level = $level;
        $this->file = $file;
        $this->output = $output;
        parent::__construct('', $this->createHandlers());
    }

    protected function createHandlers()
    {
        return [
            new FileHandler($this->eventLoop, $this->file, $this->level),
            new ConsoleHandler($this->eventLoop, $this->output, $this->level),
        ];
    }
}