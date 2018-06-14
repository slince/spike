<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Client\Timer;

use Spike\Common\Protocol\Spike;

/**
 * @codeCoverageIgnore
 */
class Heartbeat extends Timer
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

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return true;
    }
}