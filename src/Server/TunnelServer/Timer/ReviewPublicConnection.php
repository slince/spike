<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\TunnelServer\Timer;

class ReviewPublicConnection extends PeriodicTimer
{
    public function __invoke()
    {
        var_dump(count($this->tunnelServer->getPublicConnections()));
        foreach ($this->tunnelServer->getPublicConnections() as $key => $publicConnection) {
            if ($publicConnection->getWaitingDuration() > 60) {
                $this->tunnelServer->closePublicConnection($publicConnection, 'Waiting for more than 60 seconds without responding');
                $this->tunnelServer->getPublicConnections()->remove($key);
            }
        }
    }

    public function getInterval()
    {
        return 1 * 2;
    }
}