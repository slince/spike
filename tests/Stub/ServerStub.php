<?php
namespace Spike\Tests\Stub;

use React\EventLoop\Factory;
use Slince\Event\Dispatcher;
use Spike\Common\Authentication\PasswordAuthentication;
use Spike\Common\Logger\Logger;
use Spike\Server\Configuration;
use Spike\Server\Server;

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
        $configuration = new Configuration();
        $configuration->merge($config);
        parent::__construct($configuration);
    }

    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}