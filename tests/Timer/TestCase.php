<?php
namespace Spike\Tests\Timer;

use Spike\Tests\TestCase as BaseTestCase;
use Spike\Timer\UseTimerTrait;

class TestCase extends BaseTestCase
{
    use UseTimerTrait;

    public function setUp()
    {
        foreach ($this->getTimers() as $timer) {
            $timer->cancel();
        }
        parent::setUp();
    }
}