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
    protected function closeProxyConnection(ProxyConnection $proxyConnection, $message)
    {
        $proxyConnection->getConnection()->end("Timeout");
    }
}