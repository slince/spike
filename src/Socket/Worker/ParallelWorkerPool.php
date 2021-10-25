<?php

namespace Spike\Socket\Worker;

use parallel\Runtime as ParallelRuntime;
use React\EventLoop\LoopInterface;
use Spike\Socket\ServerInterface;
use Spike\Socket\Worker;
use Spike\Socket\WorkerPool;

class ParallelWorkerPool extends WorkerPool
{
    protected $runtime;

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->runtime = new ParallelRuntime();
        parent::run();
    }


    /**
     * {@inheritdoc}
     */
    public function createWorker(LoopInterface $loop, ServerInterface $server)
    {
        return new Worker($loop, $server);
    }
}