<?php

namespace Spike;

use Evenement\EventEmitter;
use Spike\Exception\InvalidArgumentException;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
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
            $loop = LoopFactory::create();
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
     * {@inheritdoc}
     */
    public function on($event, callable $listener)
    {
        if (!in_array($event, $this->options['event_names'])) {
            throw new InvalidArgumentException(sprintf('The event "%s" is not supported.', $event));
        }
        parent::on($event, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function serve()
    {
        $socket = $this->createSocket();
        $this->pool = $this->createWorkers($socket);
        $this->initialize();
        $this->runWorkers();
        $this->loop->run();
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

    protected function createSocket()
    {
        return new Server($this->options['address'], $this->loop);
    }

    protected function createWorkers($socket)
    {
        $pool = new WorkerPool();
        for ($i = 0; $i < $this->options['max_workers']; $i++) {
            $pool->add(new Worker($this->loop, $this, $socket));
        }
        return $pool;
    }

    protected function runWorkers()
    {
        foreach ($this->pool as $worker) {
            $worker->start();
        }
    }

    protected function initialize()
    {
    }
}