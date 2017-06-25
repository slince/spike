<?php
namespace Spike\Tests\Server\Fixtures\Stub;

use React\EventLoop\LoopInterface;
use Slince\Event\Dispatcher;
use Spike\Authentication\AuthenticationInterface;
use Spike\Server\EventStore;
use Spike\Server\Server;

class ServerStub extends Server
{
    public function __construct($address, AuthenticationInterface $authentication, LoopInterface $loop = null, Dispatcher $dispatcher = null)
    {
        parent::__construct($address, $authentication, $loop, $dispatcher);
    }

    public function run()
    {
        $this->dispatcher->dispatch(EventStore::SERVER_RUN);
        foreach ($this->getDefaultTimers() as $timer) {
            $this->addTimer($timer);
        }
        $this->loop->run();
    }
}