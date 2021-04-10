<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Io\Message;

class RegisterTunnelAwareHandler extends AuthAwareHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(Message $message, ConnectionInterface $connection)
    {
        parent::handle($message);
        $tunnelInfo = $message->getBody();
        $chunkServer = $this->server->getChunkServers()->findByTunnelInfo($tunnelInfo);
        if (!$chunkServer) {
            $tunnel = TunnelFactory::fromArray($tunnelInfo);
            try {
                $chunkServer = $this->createChunkServer($tunnel);
                $chunkServer->start();

                $response = new Spike('register_tunnel_response', $tunnel->toArray(), [
                    'code' => 200,
                ]);
            } catch (\Exception $exception) {
                $body = array_merge($tunnel->toArray(), [
                    'error' => iconv ('UTF-8', 'UTF-8//IGNORE', $exception->getMessage())
                ]);
                $response = new Spike('register_tunnel_response', $body, [
                    'code' => $exception->getCode() ?: 1,
                ]);
            }
        } else {
            $body = array_merge($tunnelInfo, [
                'error' => 'The tunnel has been registered'
            ]);
            $response = new Spike('register_tunnel_response', $body, [
                'code' => 1,
            ]);
        }
        $connection->write($response);
    }

    /**
     * Creates a tunnel server for the tunnel.
     *
     * @param TunnelInterface $tunnel
     *
     * @return ChunkServer\ChunkServerInterface
     */
    protected function createChunkServer(TunnelInterface $tunnel)
    {
        if ($tunnel instanceof HttpTunnel) {
            $chunkServer = new ChunkServer\HttpChunkServer($this->server, $this->client, $tunnel);
        } else {
            $chunkServer = new ChunkServer\TcpChunkServer($this->server, $this->client, $tunnel);
        }
        $this->server->getChunkServers()->add($chunkServer);

        return $chunkServer;
    }
}