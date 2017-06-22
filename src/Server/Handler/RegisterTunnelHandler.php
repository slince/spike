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
                    'code' => 0
                ]);
            } catch (\Exception $exception) {
                $response = new Spike('register_tunnel_response', array_replace($tunnel->toArray(), [
                    'error' => $exception->getMessage()
                ]), [
                    'code' => $exception->getCode() ?: 1
                ]);
            }
        } else {
            $response = new Spike('register_tunnel_response', array_replace($tunnelInfo, [
                'error' => 'The tunnel has been registered'
            ]), [
                'code' => 1
            ]);
        }
        $this->connection->write($response);
    }
}