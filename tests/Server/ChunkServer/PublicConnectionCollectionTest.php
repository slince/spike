<?php
namespace Spike\Tests\Server\ChunkServer;

use Doctrine\Common\Collections\ArrayCollection;
use Spike\Server\ChunkServer\PublicConnection;
use Spike\Tests\TestCase;

class PublicConnectionCollectionTest extends TestCase
{
    public function testFind()
    {
        $collection = new ArrayCollection();
        $this->assertCount(0, $collection);
        $publicConnection = new PublicConnection($this->getConnectionMock(), 'init buffer');
        $collection->add($publicConnection);
        $this->assertEquals($publicConnection, $collection->findById($publicConnection->getId()));
        $this->assertNull($collection->findById('not-exists'));
    }
}