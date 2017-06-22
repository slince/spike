<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\TunnelClient;


class TcpTunnelClient extends TunnelClient
{
    /**
     * {@inheritdoc}
     */
    public function handleConnectLocalError(\Exception $exception)
    {
        $this->proxyConnection->end($exception->getMessage());
        $this->close();
    }
}