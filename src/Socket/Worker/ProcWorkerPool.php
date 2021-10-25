<?php

namespace Spike\Socket\Worker;

use React\EventLoop\LoopInterface;
use Spike\Socket\ServerInterface;
use Spike\Socket\WorkerPool;

class ProcWorkerPool extends WorkerPool
{
    /**
     * {@inheritdoc}
     */
    public function createWorker(LoopInterface $loop, ServerInterface $server)
    {
        return new ProcWorker($loop, $server);
    }
}