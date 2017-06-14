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

class StartProxyHandler extends MessageHandler
{
    public function handle(MessageInterface $message)
    {
        $tunnelInfo = $message->getBody();
        $tunnel = $this->client->findTunnel($tunnelInfo);
        if ($tunnel ===  false) {
            throw new InvalidArgumentException("Can not find the matching tunnel");
        }
        if ($tunnel instanceof HttpTunnel) {
            if ($tunnel->supportProxyHost($tunnelInfo['proxyHost'])) {
                $localAddress = $tunnelInfo['proxyHost'];
            } else {
                throw new InvalidArgumentException(sprintf('The tunnel does\'t support the host "%s"', $tunnelInfo['proxyHost']));
            }
        } else {
            $localAddress = $tunnel->getHost();
        }
        $this->client->createTunnelClient($tunnel, $localAddress);
    }
}