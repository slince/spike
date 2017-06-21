<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer;

class TcpTunnelServer extends TunnelServer
{
    /**
     * {@inheritdoc}
     */
    public function closeProxyConnection(ProxyConnection $proxyConnection, $message = null)
    {
        $proxyConnection->getConnection()->end($message ?: "Timeout");
    }
}