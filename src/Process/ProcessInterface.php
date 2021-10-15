<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Process;

use React\Stream\ReadableStreamInterface;
use React\Stream\WritableStreamInterface;

interface ProcessInterface
{
    /**
     * process status,running
     * @var string
     */
    const STATUS_READY = 'ready';

    /**
     * process status,running
     * @var string
     */
    const STATUS_STARTED = 'started';

    /**
     * process status,terminated
     * @var string
     */
    const STATUS_TERMINATED = 'terminated';

    /**
     * Checks whether the process is running.
     *
     * @return bool
     */
    public function isRunning(): bool;

    /**
     * Starts the process.
     *
     * @param bool $blocking
     */
    public function start(bool $blocking = true);

    /**
     * Wait for the process exit.
     */
    public function wait();

    /**
     * Closes the process.
     */
    public function stop();

    /**
     * Gets the process id.
     *
     * @return int
     */
    public function getPid(): int;

    /**
     * Gets the std iny stream for the process.
     *
     * @return WritableStreamInterface
     */
    public function getStdin(): WritableStreamInterface;

    /**
     * Gets the std out stream for the process.
     *
     * @return ReadableStreamInterface
     */
    public function getStdout(): ReadableStreamInterface;
}