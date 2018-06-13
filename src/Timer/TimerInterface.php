<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface as ReactTimer;

interface TimerInterface
{
    /**
     * Checks whether the time is periodic
     * @return bool
     */
    public function isPeriodic();

    /**
     * Get the interval after which this timer will execute, in seconds
     * @return float
     */
    public function getInterval();

    /**
     * Cancels the timer
     */
    public function cancel();

    /**
     * activates the timer
     * @param LoopInterface $loop
     * @param ReactTimer $timer
     */
    public function activate(LoopInterface $loop, ReactTimer $timer);

    /**
     * Invokes the timer
     */
    public function __invoke();
}