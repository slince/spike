<?php

namespace Spike\Process;

interface ProcessInterface
{
    /**
     * process status,running
     * @var string
     */
    const STATUS_RUNNING = 'running';

    /**
     * process status,terminated
     * @var string
     */
    const STATUS_TERMINATED = 'terminated';

    /**
     * Send signal to the process.
     *
     * @param int $signal
     */
    public function signal($signal);

    /**
     * Register signal handler.
     *
     * @param $signal
     * @param callable $handler
     */
    public function onSignal($signal, callable $handler);

    /**
     * Starts the process.
     *
     * @param bool $blocking
     */
    public function start($blocking = true);

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
    public function getPid();
}