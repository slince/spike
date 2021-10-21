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
    /**
     * @var string
     */
    protected $cmd;
    protected $cwd;
    protected $env = [];
    protected $process;
    protected $processInformation;
    protected $exitcode;
    protected $options = ['suppress_errors' => true, 'bypass_shell' => true];

    public function __construct(string $cmd, string $cwd = null, array $env = [])
    {
        if (!\function_exists('proc_open')) {
            throw new LogicException('The Process class relies on proc_open, which is not available on your PHP installation.');
        }
        $this->cmd = $cmd;
        $this->cwd = $cwd;
        if (null === $this->cwd && (\defined('ZEND_THREAD_SAFE') || '\\' === \DIRECTORY_SEPARATOR)) {
            $this->cwd = getcwd();
        }
        $this->setEnv($env);
    }

    /**
     * Sets the environment variables.
     *
     * Each environment variable value should be a string.
     * If it is an array, the variable is ignored.
     * If it is false or null, it will be removed when
     * env vars are otherwise inherited.
     *
     * That happens in PHP when 'argv' is registered into
     * the $_ENV array for instance.
     *
     * @param array $env The new environment variables
     *
     * @return $this
     */
    public function setEnv(array $env): ProcProcess
    {
        // Process can not handle env values that are arrays
        $env = array_filter($env, function ($value) {
            return !\is_array($value);
        });

        $this->env = $env;

        return $this;
    }

    /**
     * Defines options to pass to the underlying proc_open().
     *
     * @see https://php.net/proc_open for the options supported by PHP.
     *
     * Enabling the "create_new_console" option allows a subprocess to continue
     * to run after the main process exited, on both Windows and *nix
     */
    public function setOptions(array $options)
    {
        if ($this->isRunning()) {
            throw new RuntimeException('Setting options while the process is running is not possible.');
        }

        $defaultOptions = $this->options;
        $existingOptions = ['blocking_pipes', 'create_process_group', 'create_new_console'];

        foreach ($options as $key => $value) {
            if (!\in_array($key, $existingOptions)) {
                $this->options = $defaultOptions;
                throw new LogicException(sprintf('Invalid option "%s" passed to "%s()". Supported options are "%s".', $key, __METHOD__, implode('", "', $existingOptions)));
            }
            $this->options[$key] = $value;
        }
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

    /**
     * {@inheritdoc}
     */
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
            $this->exitcode = $this->processInformation['exitcode'];
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
        $cmd = $this->cmd;
        $descriptors = $this->getDescriptors();

        $this->process = @\proc_open($cmd, $descriptors, $pipes, $this->cwd, $this->env, $this->options);

        if (!\is_resource($this->process)) {
            $error = \error_get_last();
            throw new RuntimeException(sprintf('Unable to launch a new process: %s.', $error));
        }
    }

    protected function getDescriptors(): array
    {
        if ('\\' === DIRECTORY_SEPARATOR) {

        }
        return [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function wait()
    {
        $this->requireProcessIsStarted(__FUNCTION__);
        $this->updateStatus(false);
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        if (\is_resource($this->process)) {
            $exitcode = proc_close($this->process);
            if ($this->exitcode === null && $exitcode !== -1) {
                $this->exitcode = $this->processInformation['exitcode'];
            }
        }
        $this->closePipes();
        $this->status = self::STATUS_TERMINATED;
    }

    private function closePipes()
    {
        if (null !== $this->stdin) {
            fclose($this->stdin);
        }
        if (null !== $this->stdout) {
            fclose($this->stdout);
        }
        if (null !== $this->stderr) {
            fclose($this->stderr);
        }
    }

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

    private function requireProcessIsStarted(string $functionName)
    {
        if (!$this->isStarted()) {
            throw new LogicException(sprintf('Process must be started before calling "%s()".', $functionName));
        }
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
     * @inheritDoc
     */
    public function getPid(): ?int
    {
        return $this->isRunning() ? $this->processInformation['pid'] : null;
    }

    /**
     * @inheritDoc
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

        return $this->processInformation['signaled'];
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

        return $this->processInformation['termsig'];
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

        return $this->processInformation['stopped'];
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

        return $this->processInformation['stopsig'];
    }
}