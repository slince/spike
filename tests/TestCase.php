<?php
namespace Spike\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use Spike\Client\Worker\TcpWorker;
use Spike\Common\Authentication\PasswordAuthentication;
use Spike\Common\Tunnel\TunnelFactory;
use Spike\Server\ChunkServer\TcpChunkServer;
use Spike\Server\Client;
use Spike\Server\Configuration;
use Spike\Server\Server;
use Spike\Tests\Stub\ServerStub;
use Spike\Tests\Stub\ClientStub;
use Spike\Tests\Stub\LoggerStub;

class TestCase extends BaseTestCase
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    public function setUp()
    {
        $this->loop && $this->loop->stop();
        $this->loop =  null;
    }

    public function getServerStub($config = [])
    {
        $config['loop'] = $this->getEventLoop();
        return new ServerStub($config);
    }

    /**
     * @param array $config
     * @return \Spike\Server\Server
     */
    public function getServerMock($config = [])
    {
        $defaults = [
            'address' => '127.0.0.1:8088',
            'auth' =>[
                'type' => 'simple_password',
                'username' => 'foo',
                'password' => 'bar'
            ],
        ];
        $config = array_merge($defaults, $config);

        $configuration = new Configuration();
        $configuration->merge($config);

        return $this->getMockBuilder(Server::class)
            ->setConstructorArgs([$configuration, $this->getEventLoop()])
            ->setMethods(null)
            ->getMock();
    }

    public function getClientStub()
    {
        $config['loop'] = $this->getEventLoop();
        return new ClientStub($config);
    }

    public function getConnectionMock()
    {
        return $this->getMockBuilder(ConnectionInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function getLoggerStub($level = 200)
    {
        return new LoggerStub($this->getEventLoop(), $level);
    }

    public function getEventLoop()
    {
        return $this->loop ?: ($this->loop = Factory::create());
    }

    public function getWorkerMock()
    {
        return $this->getMockBuilder(TcpWorker::class)
            ->setConstructorArgs([
                $this->getClientStub(),
                TunnelFactory::fromArray([
                    'protocol' => 'tcp',
                    'serverPort' => 8086,
                    'host' => '127.0.0.1:3306'
                ]),
                'foo-connection-id',
                '127.0.0.1:8088',
                $this->getEventLoop()
            ])
            ->setMethods(['run', 'registerProxyConnection'])
            ->getMock();
    }

    public function getChunkServerMock()
    {
        $connection = $this->getConnectionMock();
        return $this->getMockBuilder(TcpChunkServer::class)
            ->setConstructorArgs([
                $this->getServerStub(),
                new Client([], $connection),
                TunnelFactory::fromArray([
                    'protocol' => 'tcp',
                    'serverPort' => 8086,
                    'host' => '127.0.0.1:3306'
                ]),
                $this->getEventLoop()
            ])
            ->setMethods(['run', 'registerProxyConnection'])
            ->getMock();
    }
}