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

use React\Socket\ConnectionInterface;
use Spike\Command\CommandInterface;
use Spike\Server\Client;
use Spike\Server\ClientRegistry;
use Spike\Server\Server;

abstract class AuthenticationAwareHandlerServer extends ServerCommandHandler
{
    /**
     * @var ClientRegistry
     */
    protected $clients;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(Server $server, ClientRegistry $clients)
    {
        parent::__construct($server);
        $this->client = $clients;
    }

    public function checkClientId(CommandInterface $command, ConnectionInterface $connection)
    {
        $id = $command->getArgument('client-id');
        $client = $this->clients->get($id);
        $client->refresh();
        if (null === $client) {


            $connection->end(new ErrorMessage('Cannot find the client'));
        } else {
            $this->client = $client;
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}