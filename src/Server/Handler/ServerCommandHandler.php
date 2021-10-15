<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use Spike\Handler\CommandHandler;
use Spike\Server\ClientRegistry;
use Spike\Server\Server;

abstract class ServerCommandHandler extends CommandHandler
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var ClientRegistry
     */
    protected $clients;

    public function __construct(Server $server, ClientRegistry $clients)
    {
        $this->server = $server;
        $this->clients = $clients;
    }
}