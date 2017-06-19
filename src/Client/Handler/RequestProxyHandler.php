<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Spike\Client\ProxyContext;
use Spike\Client\Tunnel\HttpTunnel;
use Spike\Exception\InvalidArgumentException;
use Spike\Protocol\MessageInterface;

class RequestProxyHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $tunnelInfo = $message->getBody();
        $tunnel = $this->client->findTunnel($tunnelInfo);
        if ($tunnel instanceof HttpTunnel) {
            $tunnel->setProxyHost($tunnelInfo['proxyHost']);
        }
        $this->client->createTunnelClient($tunnel);
    }
}