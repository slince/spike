<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\ChunkServer\Timer;

/**
 * @codeCoverageIgnore
 */
class PublicConnectionScanTimer extends ChunkServerTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        foreach ($this->chunkServer->getPublicConnections() as $publicConnection) {
            if ($publicConnection->getWaitingDuration() > 60) {
                $this->chunkServer->closePublicConnection($publicConnection, 'Waiting for more than 60 seconds without responding');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return  60 * 1;
    }
}