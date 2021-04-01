<?php


namespace Spike\Command;

use Spike\Server\Server;
use Symfony\Component\Console\Command\Command;

class ServerCommand extends Command
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
        parent::__construct(null);
    }
}