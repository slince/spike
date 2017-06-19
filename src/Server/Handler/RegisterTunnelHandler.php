<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Protocol\Spike;
use Spike\Tunnel\TunnelFactory;

class RegisterTunnelHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $tunnel = TunnelFactory::fromArray($message->getBody());
        $tunnel->setControlConnection($this->connection);
        try {
            $this->server->createTunnelServer($tunnel, $this->connection);
            $response = new Spike('register_tunnel_response', $tunnel->toArray(), [
                'code' => 0
            ]);
        } catch (\Exception $exception) {
            $response = new Spike('register_tunnel_response', array_replace($tunnel->toArray(), [
                'error' => $exception->getMessage()
            ]), [
                'code' => $exception->getCode() ?: 1
            ]);
        }
        $this->connection->write($response);
    }
}