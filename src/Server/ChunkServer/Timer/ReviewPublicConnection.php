<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\ChunkServer\Timer;

/**
 * @codeCoverageIgnore
 */
class ReviewPublicConnection extends PeriodicTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        foreach ($this->tunnelServer->getPublicConnections() as $key => $publicConnection) {
            if ($publicConnection->getWaitingDuration() > 120) {
                $this->tunnelServer->closePublicConnection($publicConnection, 'Waiting for more than 60 seconds without responding');
                $this->tunnelServer->getPublicConnections()->remove($key);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return  60 * 1;
    }
}