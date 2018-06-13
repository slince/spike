<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface as ReactTimer;

abstract class Timer implements TimerInterface
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var ReactTimer
     */
    protected $reactTimer;

    /**
     * {@inheritdoc}
     */
    public function activate(LoopInterface $loop, ReactTimer $timer)
    {
        $this->loop = $loop;
        $this->reactTimer = $timer;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel()
    {
        $this->loop->cancelTimer($this->reactTimer);
    }
}