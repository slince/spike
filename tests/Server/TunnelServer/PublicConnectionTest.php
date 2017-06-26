<?php
namespace Spike\Tests\Server\TunnelServer;

use React\Socket\ConnectionInterface;
use Spike\Server\TunnelServer\PublicConnection;
use Spike\Tests\TestCase;

class PublicConnectionTest extends TestCase
{
    public function testGetter()
    {
        $publicConnection = new PublicConnection($this->getConnectionMock(), 'init buffer');
        $this->assertEquals('init buffer', $publicConnection->getInitBuffer());
        $this->assertNotNull($publicConnection->getId());
        $this->assertEquals($publicConnection->getId(), $publicConnection->getId());
        $this->assertInstanceOf(ConnectionInterface::class, $publicConnection->getConnection());
        $this->assertGreaterThan(0, $publicConnection->getWaitingDuration());

        $publicConnection->setInitBuffer('foo');
        $this->assertEquals('foo', $publicConnection->getInitBuffer());
    }
}