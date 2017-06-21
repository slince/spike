<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

abstract class PeriodicTimer extends Timer
{
    public function __construct()
    {
        $this->periodic = true;
    }
}