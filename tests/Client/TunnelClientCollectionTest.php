<?php
namespace Spike\Tests\Server;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Spike\Client\TunnelClientCollection;

class TunnelClientCollectionTest extends TestCase
{
    public function testConstruct()
    {
        $clients = new TunnelClientCollection();
        $this->assertInstanceOf(ArrayCollection::class, $clients);
    }
}