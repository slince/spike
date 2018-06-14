<?php
namespace Spike\Tests\Stub;

use React\EventLoop\Factory;
use Slince\Event\Dispatcher;
use Spike\Client\Client;

class ClientStub extends Client
{
    public function __construct($config = [])
    {
        $defaults = [
            'serverAddress'  => '127.0.0.1:8088',
            'tunnels' => [
                [
                    'protocol' => 'tcp',
                    'host' => '127.0.0.1:3306',
                    'serverPort' => '8086'
                ],
                [
                    'protocol' => 'http',
                    'proxyHosts' => [
                        'www.foo.com' => '127.0.0.1:80',
                        'www.bar.com' => '127.0.0.1:8080'
                    ],
                    'serverPort' => '8087'
                ],
            ],
            'auth' => [
                'username' => 'spike',
                'password' => 'spike',
            ],
            'loop' => Factory::create(),
            'dispatcher' => new Dispatcher()
        ];
        $config = array_merge($defaults, $config);
        parent::__construct($config['serverAddress'], $config['tunnels'], $config['auth'], $config['loop'], $config['dispatcher']);
    }

    public function setClientId($id)
    {
        $this->id = $id;
    }
}