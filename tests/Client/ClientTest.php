<?php
namespace Spike\Tests\Client;

use React\EventLoop\LoopInterface;
use Slince\EventDispatcher\Dispatcher;
use Spike\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetter()
    {
        $client = $this->getClientStub();
        $this->assertInstanceOf(Dispatcher::class, $client->getEventDispatcher());
        $this->assertCount(2, $client->getConfiguration()->getTunnels());
        $this->assertInstanceOf(LoopInterface::class, $client->getEventLoop());
        $this->assertNull($client->getLogger());
        $this->assertNull($client->getControlConnection());
        $this->assertCount(0, $client->getWorkers());
    }
}