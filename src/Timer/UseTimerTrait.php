<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use React\EventLoop\LoopInterface;

trait UseTimerTrait
{
    /**
     * @var TimerInterface[]
     */
    protected $timers;

    /**
     * Add one timer
     * @param TimerInterface $timer
     */
    public function addTimer(TimerInterface $timer)
    {
        if ($timer->isPeriodic()) {
            $reactTimer = $this->getLoop()->addPeriodicTimer($timer->getInterval(), $timer);
        } else {
            $reactTimer = $this->getLoop()->addTimer($timer->getInterval(), $timer);
        }
        $timer->activate($this->getLoop(), $reactTimer);
        $this->timers[] =  $timer;
    }

    /**
     * @return TimerInterface[]
     */
    public function getTimers()
    {
        return $this->timers;
    }

    /**
     * @return LoopInterface
     */
    abstract function getLoop();
}