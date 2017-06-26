<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Timer;

use Spike\Protocol\Spike;

/**
 * @codeCoverageIgnore
 */
class Heartbeat extends PeriodicTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $this->client->getControlConnection()->write(new Spike('ping'));
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 50;
    }
}