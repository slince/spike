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

use React\EventLoop\LoopInterface;

trait TimersAware
{
    /**
     * Array of timers
     *
     * @var TimerInterface[]
     */
    private $timers =  [];

    /**
     * @var array
     */
    private $reactTimers = [];

    /**
     * Add one timer
     * @param TimerInterface $timer
     */
    public function addTimer(TimerInterface $timer)
    {
        $eventLoop = $this->getEventLoop();
        if ($timer->isPeriodic()) {
            $reactTimer = $eventLoop->addPeriodicTimer($timer->getInterval(), $timer);
        } else {
            $reactTimer = $eventLoop->addTimer($timer->getInterval(), $timer);
        }
        $this->timers[] =  $timer;
        $this->reactTimers[spl_object_hash($timer)] = $reactTimer;
    }

    /**
     * Cancel Timer
     * @param TimerInterface $timer
     */
    public function cancelTimer(TimerInterface $timer)
    {
        $id = spl_object_hash($timer);
        if (isset($this->reactTimers[$id])) {
            $this->getEventLoop()->cancelTimer($this->reactTimers[$id]);
        }
    }

    /**
     * @return TimerInterface[]
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * Gets the loop instance
     *
     * @return LoopInterface
     */
    abstract public function getEventLoop();
}