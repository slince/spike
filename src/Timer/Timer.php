<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface as ReactTimer;

abstract class Timer implements TimerInterface
{
    /**
     * @param int|float $interval
     */
    protected $interval;

    protected $periodic;

    /**
     * @var LoopInterface
     */
    protected $loop;

    protected $reactTimer;

    /**
     * @return int|float
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param int|float $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    public function isPeriodic()
    {
        return $this->periodic;
    }

    public function activate(LoopInterface $loop, ReactTimer $timer)
    {
        $this->loop = $loop;
        $this->reactTimer = $timer;
    }

    public function cancel()
    {
        $this->loop->cancelTimer($this->reactTimer);
    }
}