<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\SpikeInterface;
use Spike\Protocol\Spike;
use Spike\Tunnel\TunnelFactory;

class RegisterTunnelHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        $tunnelInfo = $message->getBody();
        $tunnelServer = $this->server->getTunnelServers()->findByTunnelInfo($tunnelInfo);
        if (!$tunnelServer) {
            $tunnel = TunnelFactory::fromArray($tunnelInfo);
            try {
                $this->server->createTunnelServer($tunnel, $this->connection);
                $response = new Spike('register_tunnel_response', $tunnel->toArray(), [
                    'Code' => 0
                ]);
            } catch (\Exception $exception) {
                $response = new Spike('register_tunnel_response', array_merge($tunnel->toArray(), [
                    'error' => $exception->getMessage()
                ]), [
                    'Code' => $exception->getCode() ?: 1
                ]);
            }
        } else {
            $response = new Spike('register_tunnel_response', array_merge($tunnelInfo, [
                'error' => 'The tunnel has been registered'
            ]), [
                'Code' => 1
            ]);
        }
        $this->connection->write($response);
    }
}