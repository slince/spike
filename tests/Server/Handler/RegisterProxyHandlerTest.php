<?php
namespace Spike\Tests\Server\Handler;

use Spike\Common\Exception\BadRequestException;
use Spike\Common\Protocol\Spike;
use Spike\Server\ChunkServer\PublicConnection;
use Spike\Server\Client;
use Spike\Server\Handler\RegisterProxyAwareHandler;
use Spike\Tests\TestCase;

class RegisterProxyHandlerTest extends TestCase
{
    public function testExecute()
    {
        $chunkServer = $this->getChunkServerMock();

        $client = $chunkServer->getClient();
        $server = $chunkServer->getServer();

        $server->getClients()->add($client);
        $server->getChunkServers()->add($chunkServer);
        //fake public connection
        $publicConnection = new PublicConnection($this->getConnectionMock());
        $chunkServer->getPublicConnections()->add($publicConnection);

        $handler = new RegisterProxyAwareHandler($server, $client->getControlConnection());


        $message = new Spike('register_proxy', [
            'serverPort' => 8086
        ], [
            'client-id' => $client->getId(),
            'public-connection-id' => $publicConnection->getId()
        ]);
        $handler->handle($message);

        $message = new Spike('register_proxy', [
            'serverPort' => 9999999
        ], [
            'client-id' => $client->getId(),
            'public-connection-id' => $publicConnection->getId()
        ]);
        $this->expectException(BadRequestException::class);
        $handler->handle($message);
    }
}