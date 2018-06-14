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

use Spike\Common\Protocol\Spike;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Common\Tunnel\HttpTunnel;
use Spike\Common\Tunnel\TunnelFactory;
use Spike\Common\Tunnel\TunnelInterface;
use Spike\Server\ChunkServer;

class RegisterTunnelHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        $tunnelInfo = $message->getBody();
        $chunkServer = $this->server->getChunkServers()->findByTunnelInfo($tunnelInfo);
        if (!$chunkServer) {
            $tunnel = TunnelFactory::fromArray($tunnelInfo);
            try {
                $chunkServer = $this->createChunkServer($tunnel);
                $response = new Spike('register_tunnel_response', $tunnel->toArray(), [
                    'code' => 200,
                ]);
                $chunkServer->start();
            } catch (\Exception $exception) {
                $response = new Spike('register_tunnel_response', array_merge($tunnel->toArray(), [
                    'error' => $exception->getMessage(),
                ]), [
                    'code' => $exception->getCode() ?: 1,
                ]);
            }
        } else {
            $response = new Spike('register_tunnel_response', array_merge($tunnelInfo, [
                'error' => 'The tunnel has been registered',
            ]), [
                'code' => 1,
            ]);
        }
        $this->sendToClient($response);
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