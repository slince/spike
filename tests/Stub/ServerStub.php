<?php
namespace Spike\Tests\Stub;

use React\EventLoop\Factory;
use Slince\Event\Dispatcher;
use Spike\Server\EventStore;
use Spike\Server\Server;
use Spike\Authentication\PasswordAuthentication;

class ServerStub extends Server
{
    public function __construct($config)
    {
        $defaults = [
            'address' => '127.0.0.1:8088',
            'authentication' => new PasswordAuthentication([
                'username' => 'foo',
                'password' => 'bar'
            ]),
            'loop' => Factory::create(),
            'dispatcher' => new Dispatcher()
        ];
        $config = array_merge($defaults, $config);
        parent::__construct($config['address'], $defaults['authentication']
            , $config['loop'], $config['dispatcher']);
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