<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Timer;

class ReviewClient extends PeriodicTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        foreach ($this->server->getClients() as $client) {
            if ($client->getSilentDuration() > 60 * 5) {
                $this->server->closeClient($client);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 30 * 60;
    }
}