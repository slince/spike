<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

abstract class PeriodicTimer extends Timer
{
    /**
     * {@inheritdoc}
     */
    final public function isPeriodic()
    {
        return true;
    }
}