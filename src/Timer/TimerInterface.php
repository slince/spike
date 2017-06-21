<?php
/**
 * Created by PhpStorm.
 * User: taosikai
 * Date: 2017/6/21
 * Time: 16:04
 */

namespace Spike\Timer;

use React\EventLoop\LoopInterface;
use React\EventLoop\Timer\TimerInterface as ReactTimer;

interface TimerInterface
{
    /**
     * Determine whether the time is periodic
     *
     * @return bool
     */
    public function isPeriodic();

    /**
     * Get the interval after which this timer will execute, in seconds
     * @return float
     */
    public function getInterval();


    public function cancel();

    public function activate(LoopInterface $loop, ReactTimer $timer);

    public function __invoke();
}