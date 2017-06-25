<?php
namespace Spike\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use React\EventLoop\Factory;
use React\Socket\ConnectionInterface;
use Spike\Authentication\PasswordAuthentication;
use Spike\Logger\Logger;
use Spike\Server\Server;
use Spike\Tests\Server\Fixtures\Stub\ServerStub;
use Spike\Tests\Stub\LoggerStub;
use Symfony\Component\Console\Output\StreamOutput;

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

    public function getTunnelServerMock()
    {
        return $this->getMockBuilder(ConnectionInterface::class)
            ->setMethods(['run'])
            ->getMock()
            ->method('run')
            ->will($this->returnSelf());
    }
}