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
        $this->assertInstanceOf(ConnectionInterface::class, $client->getConnection());
        $this->assertArrayHasKey('OS', $client->toArray());
        $this->assertArrayHasKey('id', $client->toArray());
    }

    public function testControlConnection()
    {
        $client = new Client([
            'OS' => 'win'
        ], $this->createMock(ConnectionInterface::class));
        $connection = $client->getConnection();
        $client->setConnection($this->createMock(ConnectionInterface::class));
        $this->assertFalse($connection === $client->getConnection());
    }
}