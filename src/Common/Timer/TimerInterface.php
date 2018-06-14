<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Common\Timer;

interface TimerInterface
{
    /**
     * Checks whether the time is periodic.
     *
     * @return bool
     */
    public function isPeriodic();

    /**
     * Get the interval after which this timer will execute, in seconds.
     *
     * @return float
     */
    public function getInterval();

    /**
     * Invokes the timer.
     */
    public function __invoke();
}