<?php
namespace Spike\Tests\Server;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Spike\Server\ClientCollection;
use Spike\Server\Client;
use React\Socket\ConnectionInterface;

class ClientCollectionTest extends TestCase
{
    public function testConstruct()
    {
        $clients = new ClientCollection();
        $this->assertInstanceOf(ArrayCollection::class, $clients);
    }

    public function testFind()
    {
        $clients = new ClientCollection();
        $connection = $this->createMock(ConnectionInterface::class);
        $client1 = new Client([
            'OS' => 'win'
        ], $connection);

        $client2 = new Client([
            'OS' => 'win'
        ], $this->createMock(ConnectionInterface::class));
        $clients->add($client1);
        $clients->add($client2);
        $this->assertCount(2, $clients);

        $this->assertEquals($client1, $clients->findByConnection($connection));
        $this->assertFalse($clients->findByConnection($this->createMock(ConnectionInterface::class)));
        $this->assertEquals($client2, $clients->findById($client2->getId()));
        $this->assertFalse($clients->findById('foo'));
    }
}