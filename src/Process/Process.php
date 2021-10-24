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

use Spike\Exception\InvalidArgumentException;
use Spike\Exception\LogicException;
use Spike\Exception\RuntimeException;
use Spike\Process\Fifo\Fifo;

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

    protected $statusInfo;

    protected $exitcode;

    /**
     * @var array
     */
    protected $signalHandlers;

    protected $isChildProcess = false;

    protected $stdinFifo;
    protected $stdoutFifo;
    protected $stderrFifo;

    public function __construct(callable $callback)
    {
        if (!function_exists('pcntl_fork')) {
            throw new RuntimeException(sprintf('The Process class relies on ext-pcntl, which is not available on your PHP installation.'));
        }
        $this->callback = $callback;
        $this->stdinFifo = $this->createFifo();
        $this->stdoutFifo = $this->createFifo();
        $this->stderrFifo = $this->createFifo();
    }

    /**
     * Checks whether support signal.
     * @return bool
     */
    public static function isSupportPosixSignal(): bool
    {
        return function_exists('pcntl_signal');
    }

    protected function createFifo(): string
    {
        return sys_get_temp_dir() . '/' . 'sl_' . mt_rand(0, 999) . '.pipe';
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
            $this->status = self::STATUS_STARTED;
            $this->stdin = (new Fifo($this->stdinFifo, 'w'))->getStream();
            $this->stdout = (new Fifo($this->stdoutFifo, 'r'))->getStream();
            $this->stderr = (new Fifo($this->stderr, 'r'))->getStream();
            $this->updateStatus($blocking);
        } else {
            $this->isChildProcess = true;
            $this->pid = posix_getpid();
            $this->installSignalHandlers();
            $stdin = (new Fifo($this->stdinFifo, 'r'))->getStream();
            $stdout = (new Fifo($this->stdoutFifo, 'w'))->getStream();
            $stderr = (new Fifo($this->stderr, 'w'))->getStream();
            try {
                $exitCode = call_user_func($this->callback, $stdin, $stdout, $stderr);
            } catch (\Exception $e) {
                $exitCode  = 255;
            }
            exit(intval($exitCode));
        }
    }

    /**
     * Registers a callback for some signals
     * @param int|array $signals a signal or an array of signals
     * @param callable|int $handler
     */
    public function registerSignal($signals, $handler)
    {
        if (!is_array($signals)) {
            $signals = [$signals];
        }
        foreach ($signals as $signal) {
            $this->setSignalHandler($signal, $handler);
        }
    }

    protected function setSignalHandler($signal, $handler)
    {
        if (!is_int($handler) && !is_callable($handler)) {
            throw new InvalidArgumentException('The signal handler should be called or a number');
        }
        $this->signalHandlers[$signal] = $handler;
    }

    protected function installSignalHandlers()
    {
        foreach ($this->signalHandlers as $signal => $signalHandler) {
            pcntl_signal($signal, $signalHandler);
        }
    }

    /**
     * Gets the handler for a signal
     * @param int $signal
     * @return int|string
     */
    public function getHandler(int $signal)
    {
        if ($this->isChildProcess) {
            return pcntl_signal_get_handler($signal);
        }
        return $this->signalHandlers[$signal];
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
    public function close()
    {
        $this->signal(SIGKILL);
        $this->status = self::STATUS_TERMINATED;
    }

    /**
     * {@inheritdoc}
     */
    public function getPid(): int
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
    public function isRunning(): bool
    {
        //if process is not running, return false
        if (self::STATUS_STARTED !== $this->status) {
            return false;
        }
        //if the process is running, update process status again
        $this->updateStatus(false);
        return $this->running;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateStatus(bool $blocking)
    {
        if (!$this->running) {
            return;
        }
        $options = $blocking ? 0 : WNOHANG | WUNTRACED;
        $result = pcntl_waitpid($this->getPid(), $this->statusInfo, $options);
        if ($result == -1) {
            throw new RuntimeException("Error waits on or returns the status of the process");
        } elseif ($result === 0) {
            $this->running = true;
        } else {
            //The process is terminated
            $this->running = false;
            $this->status = self::STATUS_TERMINATED;

            if (pcntl_wifexited($this->statusInfo)) {
                $this->exitcode = pcntl_wexitstatus($this->statusInfo);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function terminate(int $signal = null): bool
    {
        $this->status = self::STATUS_TERMINATED;
        $this->signal($signal);
    }

    /**
     * Ensures the process is terminated, throws a LogicException if the process has a status different than "terminated".
     *
     * @throws LogicException if the process is not yet terminated
     */
    private function requireProcessIsTerminated(string $functionName)
    {
        if (!$this->isTerminated()) {
            throw new LogicException(sprintf('Process must be terminated before calling "%s()".', $functionName));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExitCode(): ?int
    {
        $this->updateStatus(false);

        return $this->exitcode;
    }

    /**
     * Returns true if the child process has been terminated by an uncaught signal.
     *
     * It always returns false on Windows.
     *
     * @return bool
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenSignaled(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wifsignaled($this->statusInfo);
    }

    /**
     * Returns the number of the signal that caused the child process to terminate its execution.
     *
     * It is only meaningful if hasBeenSignaled() returns true.
     *
     * @return int
     *
     * @throws RuntimeException In case --enable-sigchild is activated
     * @throws LogicException   In case the process is not terminated
     */
    public function getTermSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wtermsig($this->statusInfo);
    }

    /**
     * Returns true if the child process has been stopped by a signal.
     *
     * It always returns false on Windows.
     *
     * @return bool
     *
     * @throws LogicException In case the process is not terminated
     */
    public function hasBeenStopped(): bool
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wifstopped($this->statusInfo);
    }

    /**
     * Returns the number of the signal that caused the child process to stop its execution.
     *
     * It is only meaningful if hasBeenStopped() returns true.
     *
     * @return int
     *
     * @throws LogicException In case the process is not terminated
     */
    public function getStopSignal(): int
    {
        $this->requireProcessIsTerminated(__FUNCTION__);

        return pcntl_wstopsig($this->statusInfo);
    }
}