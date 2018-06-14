<?php
namespace Spike\Tests\Client\Worker;

use Spike\Tests\TestCase;

class WorkerTest extends TestCase
{
    public function testGetter()
    {
        $client = $this->getWorkerMock();
        $this->assertEquals(8086, $client->getTunnel()->getServerPort());
    }
}