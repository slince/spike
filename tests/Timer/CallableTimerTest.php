<?php
namespace Spike\Tests\Timer;

use Spike\Timer\CallableTimer;

class CallableTimerTest extends TestCase
{
    public function testConstruct()
    {
        $foo = 'foo';
        $timer = new CallableTimer(0.1, function() use (&$foo){
            $foo = 'bar';
        });
        $this->addTimer($timer);
        $this->assertEquals('foo', $foo);
        $this->loop->run();
        usleep(0.2);
        $this->assertEquals('bar', $foo);
    }

    public function testCancel()
    {
        $foo = 'foo';
        $timer = new CallableTimer(0.1, function() use (&$foo){
            $foo = 'bar';
        });
        $this->addTimer($timer);
        $timer->cancel();
        $this->assertEquals('foo', $foo);
        $this->loop->run();
        usleep(0.2);
        $this->assertEquals('foo', $foo);
    }

    public function testSetter()
    {
        $timer = new CallableTimer(0.1, function(){
        });
        $this->assertEquals(0.1, $timer->getInterval());
        $timer->setInterval(0.2);
        $this->assertEquals(0.2, $timer->getInterval());
    }
}