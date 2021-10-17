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

class FakeProcess extends AbstractProcess
{
    /**
     * @var callable
     */
    protected $callback;

    protected $running = false;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc }
     */
    public function start(bool $blocking = true)
    {
        $this->running = true;
        call_user_func($this->callback);
        $this->running = false;
    }

    /**
     * {@inheritdoc }
     */
    public function stop()
    {
        $this->running = false;
    }

    /**
     * {@inheritdoc}
     */
    public function wait()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function signal($signal)
    {
        // ignore
    }

    /**
     * {@inheritdoc}
     */
    public function getPid(): int
    {
        return getmygid();
    }

    /**
     * {@inheritdoc}
     */
    public function isRunning(): bool
    {
        return $this->running;
    }

    /**
     * {@inheritdoc}
     */
    protected function updateStatus(bool $blocking)
    {

    }
}