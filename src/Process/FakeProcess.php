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

class FakeProcess implements ProcessInterface
{
    /**
     * @var callable
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * {@inheritdoc }
     */
    public function start(bool $blocking = true)
    {
        call_user_func($this->callback);
    }

    /**
     * {@inheritdoc }
     */
    public function stop()
    {
    }

    /**
     * {@inheritdoc }
     */
    public function wait()
    {
    }

    /**
     * {@inheritdoc }
     */
    public function signal($signal)
    {
    }

    /**
     * {@inheritdoc }
     */
    public function onSignal($signal, callable $handler)
    {

    }

    /**
     * {@inheritdoc }
     */
    public function getPid()
    {
        return getmygid();
    }
}