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

abstract class AbstractProcess implements ProcessInterface
{
    /**
     * @var string
     */
    protected $status = self::STATUS_READY;

    /**
     * @var resource
     */
    public $stdin;

    /**
     * @var resource
     */
    public $stdout;

    /**
     * @var resource
     */
    public $stderr;

    /**
     * {@inheritdoc}
     */
    public function getStdin()
    {
        return $this->stdin;
    }

    /**
     * {@inheritdoc}
     */
    public function getStdout()
    {
        return $this->stdout;
    }

    /**
     * {@inheritdoc}
     */
    public function getStderr()
    {
        return $this->stderr;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return self::STATUS_READY != $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isTerminated(): bool
    {
        $this->updateStatus(false);

        return self::STATUS_TERMINATED == $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): string
    {
        $this->updateStatus(false);

        return $this->status;
    }

    /**
     * Updates the status of the process
     * @param bool $blocking
     */
    abstract protected function updateStatus(bool $blocking);
}