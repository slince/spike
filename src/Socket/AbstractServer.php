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

namespace Spike\Socket;

use Evenement\EventEmitter;
use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\ServerInterface as SocketServer;
use Spike\Exception\InvalidArgumentException;
use React\EventLoop\LoopInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractServer extends EventEmitter implements ServerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var WorkerPool|Worker
     */
    protected $pool;

    /**
     * @var SocketServer
     */
    protected $socket;

    /**
     * @var LoopInterface
     */
    protected $loop;

    public function __construct(?LoopInterface $loop = null)
    {
        if (null === $loop) {
            $loop = Loop::get();
        }
        $this->loop = $loop;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(array $options)
    {
        $optionsResolver = new OptionsResolver();
        $this->configureOptions($optionsResolver);
        $this->options = $optionsResolver->resolve($options);
    }

    /**
     * Configure options resolver for the server.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'max_workers' => 1,
                'event_names' => ['start', 'end', 'connection'],
            ])
            ->setRequired(['address']);
    }

    /**
     * {@inheritdoc}
     */
    public function on($event, callable $listener)
    {
        if (!in_array($event, $this->options['event_names'])) {
            throw new InvalidArgumentException(sprintf('The event "%s" is not supported.', $event));
        }
        return parent::on($event, $listener);
    }

    /**
     * @internal
     * @param ConnectionInterface $connection
     */
    public function handleConnection(ConnectionInterface $connection)
    {
        $this->emit('connection', [$connection]);
    }

    /**
     * {@inheritdoc}
     */
    public function pause()
    {
        $this->socket && $this->socket->pause();
    }

    /**
     * {@inheritdoc}
     */
    public function resume()
    {
        $this->socket && $this->socket->resume();
    }

    /**
     * {@inheritdoc}
     */
    public function serve()
    {
        $this->boot();
        $this->pool->run();
        $this->loop->run();
    }

    protected function boot()
    {
        $socket = $this->createSocket($this->options['address'], $this->loop);
        $this->pool = $this->createWorkers($socket);
        $this->socket = $socket;
        $this->initialize();
        $this->emit('start', [$this]);
    }

    /**
     * Creates socket server for the given address.
     *
     * @param string $address
     * @param LoopInterface $loop
     * @return SocketServer
     */
    abstract protected function createSocket(string $address, LoopInterface $loop);

    /**
     * Create worker pools.
     *
     * @param SocketServer $socket
     * @return WorkerPool
     */
    protected function createWorkers(SocketServer $socket): WorkerPool
    {
        $pool = new WorkerPool();
        for ($i = 0; $i < $this->options['max_workers']; $i++) {
            $pool->add(new Worker($this->loop, $this, $socket));
        }
        return $pool;
    }

    /**
     * Initialize the server.
     */
    protected function initialize()
    {
    }

    /**
     * Gets the worker pool.
     *
     * @return WorkerPool
     * @internal
     */
    public function getPool(): WorkerPool
    {
        return $this->pool;
    }
}