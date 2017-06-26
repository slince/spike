<?php
namespace Spike\Tests\Client;

use React\EventLoop\LoopInterface;
use Slince\Event\Dispatcher;
use Spike\Logger\Logger;
use Spike\Tests\TestCase;

class ClientTest extends TestCase
{
    public function testGetter()
    {
        $client = $this->getClientStub();
        $this->assertInstanceOf(Dispatcher::class, $client->getDispatcher());
        $this->assertCount(2, $client->getTunnels());
        $this->assertInstanceOf(LoopInterface::class, $client->getLoop());
        $this->assertNull($client->getLogger());
        $client->setLogger($this->getLoggerStub());
        $this->assertInstanceOf(Logger::class, $client->getLogger());
    }
}