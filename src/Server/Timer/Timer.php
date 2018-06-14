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

use Spike\Common\Timer\TimerInterface;
use Spike\Server\Server;

abstract class Timer implements TimerInterface
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * @return Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return true;
    }
}