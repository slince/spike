<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;
use Spike\Protocol\RegisterTunnelResponse;
use Spike\Protocol\Spike;
use Spike\Server\Tunnel\TunnelFactory;

class RegisterTunnelHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $tunnel = TunnelFactory::fromArray($message->getBody());
        $response = new Spike('auth_response', $tunnel->toArray());
        try {
            $this->server->createTunnelServer($tunnel);
            $response = new Spike('auth_response', $tunnel->toArray(), [
                'code' => 0
            ]);
        } catch (\Exception $exception) {
            $response = new Spike('auth_response', array_replace($tunnel->toArray(), [
                'error' => $exception->getMessage()
            ]), [
                'code' => $exception->getCode() ?: 1
            ]);
        }
        $this->connection->write($response);
    }
}