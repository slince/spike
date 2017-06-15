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
        $localAddress = isset($tunnelInfo['proxyHost']) ?
            $tunnelInfo['proxyHost'] : $tunnel->getHost();
        echo '##';
        $this->connection->removeAllListeners();
//        $this->connection->on('data', function($data){
//            var_dump($data);exit;
//        });
//        exit;
        echo count($this->client->getTunnelConnections());
        foreach ($this->client->getTunnelConnections() as $connection) {
            $connection->removeAllListeners();
        }

        $this->client->getConnection()->removeAllListeners();
//        $this->client->createTunnelClient($tunnel, $localAddress);
    }
}