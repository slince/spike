<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Timer;

class ReviewClient extends Timer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        foreach ($this->server->getClients() as $client) {
            if (time() - $client->getActiveAt()->getTimestamp() > 60 * 5) {
                $this->server->stopClient($client);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 5 * 60;
    }
}