<?php


namespace Spike\Command;

use Spike\Application;
use Spike\Server\Server;
use Symfony\Component\Console\Command\Command;

class ServerCommand extends Command
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @return Server
     */
    protected function getServer()
    {
        if (null === $this->server) {
            $this->server = new Server();
        }
        return $this->server;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}