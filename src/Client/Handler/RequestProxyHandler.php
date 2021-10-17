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

namespace Spike\Client\Handler;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use React\Promise\PromiseInterface;
use React\Socket\Connector;
use React\Socket\ConnectionInterface as RawConnection;
use Spike\Client\Client;
use Spike\Client\Configuration;
use Spike\Client\Connection\LocalConnection;
use Spike\Client\Tunnel;
use Spike\Client\TunnelCollection;
use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Exception\BadMessageException;
use Spike\Server\Command\REQUESTPROXY;

class RequestProxyHandler extends ClientCommandHandler implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TunnelCollection
     */
    protected $tunnels;

    /**
     * @var Configuration
     */
    protected $configuration;

    public function __construct(Client $client, Configuration $configuration)
    {
        parent::__construct($client);
        $this->configuration = $configuration;
        $this->tunnels = $configuration->getTunnels();
    }

    /**
     * {@inheritdoc}
     */
    public function handle(CommandInterface $command, ConnectionInterface $connection)
    {
        $tunnel = $this->tunnels->get($command->getServerPort());
        if (null === $tunnel) {
            throw new BadMessageException(sprintf('The tunnel with server port "%d" is not found', $command->getServerPort()));
        }
        $promises = $this->createLocalConnectors($tunnel);
        foreach ($promises as $promise) {
            $promise->then(function(RawConnection $connection){
                $localConnection = new LocalConnection($connection);
                $this->createProxyConnection($localConnection);
            });
        }
    }

    protected function createProxyConnection(LocalConnection $localConnection)
    {
        $connector = new Connector(['timeout' => $this->configuration->getTimeout()]);
        $connector->connect($this->configuration->getServerAddress())
            ->then(function(RawConnection $connection) use($localConnection){
                $this->handleConnection($connection, $localConnection);
            }, function(\Exception $e){
                $this->logger->error(sprintf('Cannot connect to the server %s; error message: %s', $this->configuration->getServerAddress(), $e->getMessage()));
            });
    }

    /**
     * {@internal}
     */
    protected function handleConnection(RawConnection $connection, LocalConnection $localConnection)
    {

    }

    /**
     * Creates local connectors.
     *
     * @param Tunnel $tunnel
     * @return PromiseInterface[]
     */
    protected function createLocalConnectors(Tunnel $tunnel): array
    {
        $connectors = [];
        for ($i = 0; $i < $tunnel->getProxyPoolSize(); $i++) {
            $connectors[] = (new Connector())->connect($tunnel->getDsn());
        }
        return $connectors;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSubscribedCommands(): array
    {
        return [REQUESTPROXY::class];
    }
}