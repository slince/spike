<?php
namespace Spike\Tests\Tunnel;

use PHPUnit\Framework\TestCase;
use Spike\Tunnel\TcpTunnel;

class TunnelTest extends TestCase
{
    public function testConstruct()
    {
        $tunnel = new TcpTunnel(8086, '127.0.0.1:3306');
        $this->assertEquals(8086, $tunnel->getServerPort());
        $this->assertEquals('127.0.0.1:3306', $tunnel->getHost());
        $this->assertEquals('tcp', $tunnel->getProtocol());
        $this->assertEquals([
            'protocol' => 'tcp',
            'host' => '127.0.0.1:3306',
            'serverPort' => 8086
        ], $tunnel->toArray());
        $this->assertEquals(json_encode([
            'protocol' => 'tcp',
            'host' => '127.0.0.1:3306',
            'serverPort' => 8086
        ]), (string)$tunnel);
    }

    public function testMatch()
    {
        $tunnel = new TcpTunnel(8086, '127.0.0.1:3306');
        $this->assertTrue($tunnel->match([
            'serverPort' => 8086
        ]));}
}