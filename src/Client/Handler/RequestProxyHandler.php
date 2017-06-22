<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Spike\Tunnel\HttpTunnel;
use Spike\Protocol\SpikeInterface;

class RequestProxyHandler extends MessageHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $tunnelInfo = $message->getBody();
        $tunnel = $this->client->findTunnel($tunnelInfo);
        if ($tunnel instanceof HttpTunnel) {
            $tunnel->setProxyHost($tunnelInfo['proxyHost']);
        }
        $this->client->createTunnelClient($tunnel, $message->getHeader('Proxy-Connection-ID'));
    }
}