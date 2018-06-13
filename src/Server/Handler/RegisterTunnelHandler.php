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


use Spike\Common\Protocol\SpikeInterface;

class RegisterTunnelHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        $tunnelInfo = $message->getBody();
        $tunnelServer = $this->server->getChunkServers()->findByTunnelInfo($tunnelInfo);
        if (!$tunnelServer) {
            $tunnel = TunnelFactory::fromArray($tunnelInfo);
            try {
                $tunnelServer = $this->server->createTunnelServer($tunnel, $this->connection);
                $response = new Spike('register_tunnel_response', $tunnel->toArray(), [
                    'Code' => 0
                ]);
                $tunnelServer->run();
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