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

class SummaryWatchTimer extends ServerTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $message = sprintf('Client Total: %s; Chunk Server Total: %s',
            count($this->server->getClients()),
            count($this->server->getChunkServers())
        );
        $this->server->getLogger()->info($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 30;
    }
}