<?php
namespace Spike\Tests\Server;

use PHPUnit\Framework\TestCase;
use React\Socket\ConnectionInterface;
use Spike\Server\Client;

class ClientTest extends TestCase
{
    public function testConstruct()
    {
        $client = new Client([
            'OS' => 'win'
        ], $this->createMock(ConnectionInterface::class));
        $this->assertInstanceOf(ConnectionInterface::class, $client->getControlConnection());
        $this->assertEquals([
            'OS' => 'win'
        ], $client->getInfo());
        $this->assertArrayHasKey('OS', $client->toArray());
        $this->assertArrayHasKey('id', $client->toArray());
    }

    public function testSilent()
    {
        $client = new Client([
            'OS' => 'win'
        ], $this->createMock(ConnectionInterface::class));
        $this->assertGreaterThan(0, $client->getSilentDuration());

        $client->setLastActiveAt(microtime(true));
        $this->assertLessThan(1, $client->getSilentDuration());
    }
}