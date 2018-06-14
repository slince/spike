<?php
namespace Spike\Tests\Common\Timer;

use Spike\Common\Timer\TimerInterface;

class CallableTimer implements TimerInterface
{
    protected $periodic;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @param int|float $interval
     */
    protected $interval;

    public function __construct($interval, callable $callable, $periodic = false)
    {
        $this->callable = $callable;
        $this->interval = $interval;
        $this->periodic = $periodic;
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return $this->periodic;
    }

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

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return call_user_func($this->callable);
    }
}