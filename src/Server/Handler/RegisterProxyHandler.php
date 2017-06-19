<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Exception\BadRequestException;
use Spike\Protocol\MessageInterface;
use Spike\Protocol\Spike;
use Spike\Server\TunnelServer\TunnelServerInterface;

class RegisterProxyHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $tunnelServer = $this->findTunnelServer($message->getBody());
        $this->connection->removeAllListeners();
        $tunnelServer->registerTunnelConnection($this->connection, $message);
    }

    /**
     * @param $info
     * @return TunnelServerInterface
     */
    protected function findTunnelServer($info)
    {
        foreach ($this->server->getTunnelServers() as $tunnelServer) {
            if ($tunnelServer->getTunnel()->match($info)) {
                return $tunnelServer;
            }
        }
        throw new BadRequestException("Can not find the tunnel server");
    }
}