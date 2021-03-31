<?php


namespace Spike\Server;

use Evenement\EventEmitter;
use Spike\Worker;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface;
use React\Socket\Server as Socket;
use Spike\WorkerPool;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Server extends EventEmitter
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var WorkerPool
     */
    protected $pool;

    /**
     * @var ServerInterface
     */
    protected $socket;

    public function __construct(LoopInterface $loop = null)
    {
        if (null === $loop) {
            $loop = Factory::create();
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

    public function listen(string $address)
    {
        $socket = $this->createSocket($address);
        $this->pool = $this->createWorkers($socket);
        $this->initialize();
        $this->runWorkers();
        $this->loop->run();
    }

    protected function createSocket(string $address)
    {
        return new Socket($address, $this->loop);
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