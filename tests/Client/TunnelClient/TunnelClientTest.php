<?php
namespace Spike\Tests\Client\TunnelClient;

use Spike\Tests\TestCase;

class TunnelClientTest extends TestCase
{
    public function testGetter()
    {
        $client = $this->getTunnelClientMock();
        $this->assertEquals(8086, $client->getTunnel()->getServerPort());
    }
}