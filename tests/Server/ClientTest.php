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
        ], $client->toArray());
        $this->assertArrayHasKey('OS', $client->toArray());
        $this->assertArrayHasKey('id', $client->toArray());
    }

    public function testControlConnection()
    {
        $client = new Client([
            'OS' => 'win'
        ], $this->createMock(ConnectionInterface::class));
        $connection = $client->getControlConnection();
        $client->setControlConnection($this->createMock(ConnectionInterface::class));
        $this->assertFalse($connection === $client->getControlConnection());
    }
}