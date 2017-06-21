<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

class TcpTunnelServer extends TunnelServer
{
    /**
     * Close all proxy connections
     */
    protected function closeAllProxyConnections()
    {
        foreach ($this->proxyConnections as $proxyConnection) {
            $proxyConnection->getConnection()->end('The tunnel server has been closed');
        }
        $this->proxyConnections = [];
    }

    /**
     * Close the connection if it does not respond for more than 60 seconds
     */
    public function handleProxyConnectionTimeout()
    {
        foreach ($this->proxyConnections as $key => $proxyConnection) {
            if ($proxyConnection->getWaitingTime() > 60) {
                $proxyConnection->getConnection()->end("Timeout");
                unset($this->proxyConnections[$key]);
            }
        }
    }
}