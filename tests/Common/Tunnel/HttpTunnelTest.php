<?php
namespace Spike\Tests\Common\Tunnel;

use PHPUnit\Framework\TestCase;
use Spike\Common\Tunnel\HttpTunnel;

class HttpTunnelTest extends TestCase
{
    public function testConstruct()
    {
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:80',
            'www.bar.com' => '127.0.0.1:81',
        ]);
        $this->assertEquals(8086, $tunnel->getServerPort());
        $this->assertEquals([
            'www.foo.com' => '127.0.0.1:80',
            'www.bar.com' => '127.0.0.1:81',
        ], $tunnel->getProxyHosts());

        $this->assertEquals('http', $tunnel->getProtocol());
        $this->assertEquals([
            'protocol' => 'http',
            'proxyHosts' => [
                'www.foo.com' => '127.0.0.1:80',
                'www.bar.com' => '127.0.0.1:81',
            ],
            'serverPort' => 8086,
            'proxyHost' => null
        ], $tunnel->toArray());

        $this->assertEquals(json_encode([
            'protocol' => 'http',
            'proxyHosts' => [
                'www.foo.com' => '127.0.0.1:80',
                'www.bar.com' => '127.0.0.1:81',
            ],
            'serverPort' => 8086,
            'proxyHost' => null
        ]), (string)$tunnel);
    }

    public function testProxyHost()
    {
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:80',
            'www.bar.com' => '127.0.0.1:81',
        ]);
        $this->assertNull($tunnel->getProxyHost());
        $tunnel->setProxyHost('www.foo.com');
        $this->assertEquals('www.foo.com', $tunnel->getProxyHost());
        $this->assertTrue($tunnel->supportProxyHost('www.foo.com'));
        $this->assertFalse($tunnel->supportProxyHost('www.baz.com'));
    }

    public function testForwardHost()
    {
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:80',
            'www.bar.com' => '127.0.0.1:81',
        ]);
        $this->assertEquals('127.0.0.1:80', $tunnel->getForwardHost('www.foo.com'));
        $this->assertNull($tunnel->getForwardHost('www.baz.com'));
    }

    public function testMatch()
    {
        $tunnel = new HttpTunnel(8086, [
            'www.foo.com' => '127.0.0.1:80',
            'www.bar.com' => '127.0.0.1:81',
        ]);
        $this->assertTrue($tunnel->match([
            'serverPort' => 8086
        ]));
        $this->assertTrue($tunnel->match([
            'serverPort' => 8086,
            'proxyHost' => 'www.foo.com',
        ]));
        $this->assertFalse($tunnel->match([
            'serverPort' => 8086,
            'proxyHost' => 'www.baz.com',
        ]));
    }
}