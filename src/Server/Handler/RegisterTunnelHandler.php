<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Protocol\RegisterTunnelResponse;
use Spike\Server\Tunnel\TunnelFactory;

class RegisterTunnelHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $tunnel = TunnelFactory::fromArray($message->getTunnel());
        try {
            $this->server->createTunnelServer($tunnel);
            $response = new RegisterTunnelResponse(0, '', [
                'Tunnel-ID' => $message->getTunnel()['id']
            ]);
        } catch (\Exception $exception) {
            $response = new RegisterTunnelResponse(1, $exception->getMessage(), [
                'Tunnel-ID' => $message->getTunnel()['id']
            ]);
        }
        $this->connection->write($response);
    }
}