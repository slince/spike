<?php
namespace Spike\Tests\Common\Timer;

use Spike\Common\Timer\TimersAware;
use Spike\Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use TimersAware;

    public function setUp()
    {
        foreach ($this->getTimers() as $timer) {
            $this->cancelTimer($timer);
        }
        parent::setUp();
    }
}