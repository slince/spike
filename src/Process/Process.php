<?php

declare(strict_types=1);

namespace Spike\Process;

use Spike\Exception\RuntimeException;

class Process extends AbstractProcess
{
    /**
     * Whether the process is running
     * @var bool
     */
    protected $running = false;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * pid
     * @var int
     */
    protected $pid;

    public function __construct(callable $callback)
    {
        if (!function_exists('pcntl_fork')) {
            throw new RuntimeException(sprintf('Please install ext-pcntl'));
        }
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function start(bool $blocking = true)
    {
        if ($this->isRunning()) {
            throw new RuntimeException("The process is already running");
        }
        $pid = \pcntl_fork();
        if ($pid == -1) {
            throw new RuntimeException("Could not fork");
        } elseif ($pid) { //Records the pid of the child process
            $this->pid = $pid;
            $this->running = true;
            $blocking && $this->wait();
        } else {
            $this->pid = posix_getpid();
            $this->installSignalHandler();
            try {
                $exitCode = call_user_func($this->callback);
            } catch (\Exception $e) {
                $exitCode  = 255;
            }
            exit(intval($exitCode));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function wait()
    {
        $this->isRunning() && $this->updateStatus(true);
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        $this->signal(SIGKILL);
    }

    /**
     * {@inheritdoc}
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * {@inheritdoc}
     */
    public function signal($signal)
    {
        if (!$this->running) {
            throw new RuntimeException("The process is not currently running");
        }
        posix_kill($this->getPid(), $signal);
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning()
    {
        //if process is not running, return false
        if (!$this->running) {
            return false;
        }
        //if the process is running, update process status again
        $this->updateStatus(false);
        return $this->running;
    }

    /**
     * Updates the status of the process
     * @param bool $blocking
     * @throws RuntimeException
     */
    protected function updateStatus($blocking = false)
    {
        if (!$this->running) {
            return;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $status, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result === 0) {
            $this->running = true;
        } else {
            //The process is terminated
            $this->running = false;
        }
    }

    /**
     * Install signal handlers for the process.
     */
    protected function installSignalHandler()
    {
        foreach ($this->signalHandlers as $signal => $signalHandler) {
            pcntl_signal($signal, $signalHandler);
        }
    }
}