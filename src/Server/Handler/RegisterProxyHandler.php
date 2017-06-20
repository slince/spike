<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Exception\BadRequestException;
use Spike\Protocol\SpikeInterface;
use Spike\Protocol\Spike;
use Spike\Server\TunnelServer\TunnelServerInterface;

class RegisterProxyHandler extends MessageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $tunnelServer = $this->findTunnelServer($message->getBody());
        $this->connection->removeAllListeners();
        $tunnelServer->registerTunnelConnection($this->connection, $message);
    }

    /**
     * Finds the tunnel server of the tunnel
     * @param array $tunnelInfo
     * @return TunnelServerInterface
     */
    protected function findTunnelServer($tunnelInfo)
    {
        $tunnelServer = $this->server->getTunnelServers()
            ->filter(function(TunnelServerInterface $tunnelServer) use ($tunnelInfo){
                return $tunnelServer->getTunnel()->match($tunnelInfo);
            })->first();
        if (!$tunnelServer) {
            throw new BadRequestException("Can not find the tunnel server");
        }
        return $tunnelServer;
    }
}