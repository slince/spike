<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Event;

use Slince\EventDispatcher\Event;
use Spike\Client\ClientInterface;
use Spike\Server\ChunkServer\ChunkServerInterface;

class ClientTerminateEvent extends Event
{
    const CLOSED_BY_REMOTE = 'remote';

    const CLOSED_BY_TIMER = 'timer';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var ChunkServerInterface[]
     */
    protected $chunkServers;

    /**
     * @var string
     */
    protected $closedBy;

    public function __construct(ClientInterface $client, $chunkServers, $closedBy)
    {
        $this->client = $client;
        $this->chunkServers = $chunkServers;
        $this->closedBy = $closedBy;
        parent::__construct(Events::CLIENT_CLOSE);
    }

    /**
     * @return string
     */
    public function getClosedBy()
    {
        return $this->closedBy;
    }

    /**
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return ChunkServerInterface[]
     */
    public function getChunkServers()
    {
        return $this->chunkServers;
    }
}