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
        $duration = time() - $client->getActiveAt()->getTimestamp();

        $handler = new PingHandler($server, $this->getConnectionMock());
        $message = new Spike('ping', null, [
            'client-id' => $client->getId()
        ]);
        $this->assertGreaterThan($duration, time() - $client->getActiveAt()->getTimestamp() );
        $handler->handle($message);
        $this->assertGreaterThan(time() - $client->getActiveAt()->getTimestamp(), $duration);
    }
}