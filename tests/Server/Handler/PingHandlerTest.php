<?php
namespace Spike\Tests\Server\Handler;

use Spike\Common\Protocol\Spike;
use Spike\Server\Client;
use Spike\Server\Handler\PingHandler;
use Spike\Tests\TestCase;

class PingHandlerTest extends TestCase
{
    public function testHandle()
    {
        $client = new Client([
            'os' => PHP_OS,
            'version' => '',
        ], $this->getConnectionMock());
        $server = $this->getServerMock();
        $server->getClients()->add($client);
        $activeAt = $client->getActiveAt();

        $handler = new PingHandler($server, $this->getConnectionMock());
        $message = new Spike('ping', null, [
            'client-id' => $client->getId()
        ]);
        sleep(1);
        $this->assertEquals($activeAt, $client->getActiveAt());
        $handler->handle($message);
        $this->assertGreaterThan($activeAt, $client->getActiveAt());
    }
}