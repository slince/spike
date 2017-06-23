<?php
namespace Spike\Tests\Timer;

use PHPUnit\Framework\TestCase as BaseTestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Spike\Timer\UseTimerTrait;

class TestCase extends BaseTestCase
{
    use UseTimerTrait;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function getLoop()
    {
        return $this->loop ?: $this->loop = Factory::create();
    }

    protected function setUp()
    {
        foreach ($this->getTimers() as $timer) {
            $timer->cancel();
        }
    }
}