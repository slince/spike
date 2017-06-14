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

class RegisterProxyHandler extends Handler
{
    public function handle(MessageInterface $message)
    {
        $tunnelServer = $this->findTunnelServer($message->getBody());
        $tunnelServer->getTunnel()->setConnection($this->connection);
        $this->connection->write(new Spike('start_proxy'));
        $tunnelServer->resume();
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