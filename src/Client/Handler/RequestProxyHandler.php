<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Slince\Event\Event;
use Spike\Client\EventStore;
use Spike\Exception\InvalidArgumentException;
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
        $tunnel = $this->client->getTunnels()->findByInfo($tunnelInfo);
        if (!$tunnel) {
            throw new InvalidArgumentException('Can not find the matching tunnel');
        }
        if ($tunnel instanceof HttpTunnel) {
            $tunnel->setProxyHost($tunnelInfo['proxyHost']);
        }
        $client = $this->client->createTunnelClient($tunnel, $message->getHeader('Proxy-Connection-ID'));
        $this->getDispatcher()->dispatch(new Event(EventStore::REQUEST_PROXY, $this, [
            'tunnel' => $tunnel,
            'client' => $client
        ]));
    }
}