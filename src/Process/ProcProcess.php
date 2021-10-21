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

use Spike\Exception\LogicException;
use Spike\Exception\RuntimeException;

final class ProcProcess extends AbstractProcess
{
    protected $process;
    protected $processInformation;
    protected $exitcode;

    /**
     * {@inheritdoc}
     */
    public function isRunning(): bool
    {
        if (self::STATUS_STARTED !== $this->status) {
            return false;
        }

        $this->updateStatus(false);

        return $this->processInformation['running'];
    }


    /**
     * Sends a POSIX signal to the process.
     *
     * @param int  $signal         A valid POSIX signal (see https://php.net/pcntl.constants)
     * @param bool $throwException Whether to throw exception in case signal failed
     *
     * @return bool True if the signal was sent successfully, false otherwise
     *
     * @throws LogicException   In case the process is not running
     * @throws RuntimeException In case --enable-sigchild is activated and the process can't be killed
     * @throws RuntimeException In case of failure
     */
    private function doSignal(int $signal, bool $throwException): bool
    {
        if (null === $pid = $this->getPid()) {
            if ($throwException) {
                throw new LogicException('Can not send signal on a non running process.');
            }

            return false;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            exec(sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
            if ($exitCode && $this->isRunning()) {
                if ($throwException) {
                    throw new RuntimeException(sprintf('Unable to kill the process (%s).', implode(' ', $output)));
                }

                return false;
            }
        } else {
            if (!$this->isSigchildEnabled()) {
                $ok = @proc_terminate($this->process, $signal);
            } elseif (\function_exists('posix_kill')) {
                $ok = @posix_kill($pid, $signal);
            } elseif ($ok = proc_open(sprintf('kill -%d %d', $signal, $pid), [2 => ['pipe', 'w']], $pipes)) {
                $ok = false === fgets($pipes[2]);
            }
            if (!$ok) {
                if ($throwException) {
                    throw new RuntimeException(sprintf('Error while sending signal "%s".', $signal));
                }

                return false;
            }
        }

        $this->latestSignal = $signal;
        $this->fallbackStatus['signaled'] = true;
        $this->fallbackStatus['exitcode'] = -1;
        $this->fallbackStatus['termsig'] = $this->latestSignal;

        return true;
    }

    protected function updateStatus(bool $blocking)
    {
        if (self::STATUS_STARTED !== $this->status) {
            return;
        }

        $this->processInformation = \proc_get_status($this->process);

        if ($this->processInformation === false) {
            throw new \UnexpectedValueException('proc_get_status() failed');
        }

        if (!$this->processInformation['running'] && -1 !== $this->processInformation['exitcode']) {
            $this->exitCode = $this->processInformation['exitcode'];
        }
    }

    /**
     * @inheritDoc
     */
    public function start(bool $blocking = true)
    {
        if ($this->isRunning()) {
            throw new \RuntimeException('Process is already running');
        }
    }

    public function close()
    {
        if (\is_resource($this->process)) {
            proc_close($this->process);
        }
        fclose();
    }

    /**
     * @inheritDoc
     */
    public function wait()
    {
    }

    /**
     * @inheritDoc
     */
    public function getPid(): ?int
    {
        return $this->isRunning() ? $this->processInformation['pid'] : null;
    }
}