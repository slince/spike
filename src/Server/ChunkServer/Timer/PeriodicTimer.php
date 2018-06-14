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

use Spike\Server\ChunkServer\ChunkServerInterface;
use Spike\Timer\PeriodicTimer as BasePeriodicTimer;

abstract class PeriodicTimer extends BasePeriodicTimer
{
    protected $tunnelServer;

    public function __construct(ChunkServerInterface $tunnelServer)
    {
        $this->tunnelServer = $tunnelServer;
    }

    /**
     * @return ChunkServerInterface
     */
    public function getTunnelServer()
    {
        return $this->tunnelServer;
    }
}