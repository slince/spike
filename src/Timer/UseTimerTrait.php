<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use React\EventLoop\LoopInterface;

trait UseTimerTrait
{
    public function addTimer(TimerInterface $timer)
    {
        if ($timer->isPeriodic()) {
            $reactTimer = $this->getLoop()->addPeriodicTimer($timer->getInterval(), $timer);
        } else {
            $reactTimer = $this->getLoop()->addTimer($timer->getInterval(), $timer);
        }
        $timer->activate($this->getLoop(), $reactTimer);
    }

    /**
     * @return LoopInterface
     */
    abstract function getLoop();
}