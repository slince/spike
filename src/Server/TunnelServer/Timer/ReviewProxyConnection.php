<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer\Timer;

class ReviewProxyConnection extends PeriodicTimer
{
    protected $interval = 1 * 2;

    public function __invoke()
    {
        var_dump(count($this->tunnelServer->getProxyConnections()));
        foreach ($this->tunnelServer->getProxyConnections() as $key => $proxyConnection) {
            if ($proxyConnection->getWaitingDuration() > 60) {
                $this->tunnelServer->closeProxyConnection($proxyConnection, 'Waiting for more than 60 seconds without responding');
                $this->tunnelServer->getProxyConnections()->remove($key);
            }
        }
    }
}