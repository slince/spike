<?php

namespace Spike\Socket;

use Evenement\EventEmitter;
use React\EventLoop\Loop;
use React\Socket\ConnectionInterface;
use React\Socket\SocketServer;
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
     * @var resource
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
                'event_names' => ['start', 'end', 'client-connect']
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

    abstract protected function createSocket(string $address, LoopInterface $loop);

    protected function createWorkers($socket): WorkerPool
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