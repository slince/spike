<?php
namespace Spike\Tests\Stub;

use React\EventLoop\Factory;
use Slince\Event\Dispatcher;
use Spike\Client\Client;
use Spike\Client\Configuration;
use Spike\Common\Logger\Logger;

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
        $configuration = new Configuration();
        $configuration->merge($config);
        parent::__construct($configuration);
    }

    public function setClientId($id)
    {
        $this->id = $id;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}