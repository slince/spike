<?php
namespace Spike\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use Spike\Authentication\PasswordAuthentication;
use Spike\Client\TunnelClient\TcpTunnelClient;
use Spike\Server\Server;
use Spike\Server\TunnelServer\TcpTunnelServer;
use Spike\Tests\Stub\ServerStub;
use Spike\Tests\Stub\ClientStub;
use Spike\Tests\Stub\LoggerStub;
use Spike\Tunnel\TunnelFactory;

class TestCase extends BaseTestCase
{
    protected $loop;

    public function setUp()
    {
        $this->loop && $this->loop->stop();
        $this->loop =  null;
    }

    public function getServerStub($config = [])
    {
        $config['loop'] = $this->getLoop();
        return new ServerStub($config);
    }

    public function getServerMock($config = [])
    {
        $defaults = [
            'address' => '127.0.0.1:8088',
            'authentication' => new PasswordAuthentication([
                'username' => 'foo',
                'password' => 'bar'
            ]),
            'loop' => $this->getLoop()
        ];
        $config = array_merge($defaults, $config);
        return $this->getMockBuilder(Server::class)
            ->setConstructorArgs(array_values($config))
            ->setMethods(null)
            ->getMock();
    }

    public function getClientStub()
    {
        $config['loop'] = $this->getLoop();
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
        return new LoggerStub($level);
    }

    public function getLoop()
    {
        return $this->loop ?: ($this->loop = Factory::create());
    }

    public function getTunnelClientMock()
    {
        return $this->getMockBuilder(TcpTunnelClient::class)
            ->setConstructorArgs([
                $this->getClientStub(),
                TunnelFactory::fromArray([
                    'protocol' => 'tcp',
                    'serverPort' => 8086,
                    'host' => '127.0.0.1:3306'
                ]),
                'foo-connection-id',
                '127.0.0.1:8088',
                $this->getLoop()
            ])
            ->setMethods(['run', 'registerProxyConnection'])
            ->getMock();
    }

    public function getTunnelServerMock()
    {
        return $this->getMockBuilder(TcpTunnelServer::class)
            ->setConstructorArgs([
                $this->getServerStub(),
                $this->getConnectionMock(),
                TunnelFactory::fromArray([
                    'protocol' => 'tcp',
                    'serverPort' => 8086,
                    'host' => '127.0.0.1:3306'
                ]),
                $this->getLoop()
            ])
            ->setMethods(['run', 'registerProxyConnection'])
            ->getMock();
    }
}