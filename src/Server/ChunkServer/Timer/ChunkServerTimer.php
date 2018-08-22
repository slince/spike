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

use Spike\Common\Timer\TimerInterface;
use Spike\Server\ChunkServer\ChunkServerInterface;

abstract class ChunkServerTimer implements TimerInterface
{
    /**
     * @var ChunkServerInterface
     */
    protected $chunkServer;

    public function __construct(ChunkServerInterface $tunnelServer)
    {
        $this->chunkServer = $tunnelServer;
    }

    /**
     * @return ChunkServerInterface
     */
    public function getChunkServer()
    {
        return $this->chunkServer;
    }
}