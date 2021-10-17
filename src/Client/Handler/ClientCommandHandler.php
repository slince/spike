<?php

namespace Spike\Client\Handler;

use Spike\Client\Client;
use Spike\Handler\CommandHandler;

abstract class ClientCommandHandler extends CommandHandler
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}