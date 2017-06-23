<?php
namespace Spike\Tests\Server\TunnelServer;

use Spike\Server\TunnelServer\PublicConnection;
use Spike\Server\TunnelServer\PublicConnectionCollection;
use Spike\Tests\TestCase;

class PublicConnectionCollectionTest extends TestCase
{
    public function testFind()
    {
        $collection = new PublicConnectionCollection();
        $this->assertCount(0, $collection);
        $publicConnection = new PublicConnection($this->getConnectionMock(), 'init buffer');
        $collection->add($publicConnection);
        $this->assertEquals($publicConnection, $collection->findById($publicConnection->getId()));
    }
}