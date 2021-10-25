<?php

namespace Spike\Socket\Worker;

use parallel\Runtime as ParallelRuntime;
use parallel\Channel;
use React\EventLoop\LoopInterface;
use Spike\Socket\ServerInterface;
use Spike\Socket\Worker;

class ParallelWorker extends Worker
{
    protected $runtime;

    public function __construct(LoopInterface $loop, ServerInterface $server)
    {
        parent::__construct($loop, $server);
    }

    public function start()
    {
        $this->runtime = new ParallelRuntime();
        $this->runtime->run($this->createCallable());
    }

    protected function createCallable(): \Closure
    {
        return function($id, Channel $channel){

            $this->work();
        };
    }
}