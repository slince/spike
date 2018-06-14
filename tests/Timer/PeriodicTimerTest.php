<?php
namespace Spike\Tests\Timer;

use Spike\Timer\PeriodicTimer;

class PeriodicTimerTest extends TestCase
{
    public function testConstruct()
    {
        $count = 0;
        $timer = new PeriodicTimerTestClass(function() use (&$timer, &$count){
            if ($count >= 3) {
                $timer->cancel();
            } else {
                $count++;
            }
        });
        $this->addTimer($timer);
        $this->loop->run();
        $this->assertEquals(3, $count);
    }
}

class PeriodicTimerTestClass extends PeriodicTimer
{
    protected $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    public function __invoke()
    {
        return call_user_func($this->callable);
    }

    public function getInterval()
    {
        return 0.1;
    }
}