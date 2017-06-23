<?php
namespace Spike\Tests\Server\Handler;

use Spike\Server\Client;
use Spike\Server\Handler\PingHandler;
use Spike\Protocol\Spike;
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
        $duration = $client->getSilentDuration();

        $handler = new PingHandler($server, $this->getConnectionMock());
        $message = new Spike('ping', null, [
            'Client-ID' => $client->getId()
        ]);
        $handler->handle($message);
        $this->assertGreaterThan($client->getSilentDuration(), $duration);
    }
}