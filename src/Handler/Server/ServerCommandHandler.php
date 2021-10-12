<?php

declare(strict_types=1);

namespace Spike\Handler\Server;

use Spike\Handler\CommandHandler;
use Spike\Server\Server;

abstract class ServerCommandHandler extends CommandHandler
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }
}