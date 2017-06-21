<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

class CallableTimer extends Timer
{
    /**
     * @var callable
     */
    protected $callable;

    public function __construct($interval, callable $callable, $periodic = false)
    {
        $this->callable = $callable;
        $this->interval = $interval;
        $this->periodic = $periodic;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        return call_user_func($this->callable);
    }
}