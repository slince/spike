<?php
namespace Spike\Tests\Protocol;

use PHPUnit\Framework\TestCase;
use Spike\Protocol\Spike;
use Spike\Protocol\SpikeInterface;

class SpikeTest extends TestCase
{
    public function testConstruct()
    {
        $message = new Spike('foo', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $this->assertEquals('foo', $message->getAction());
        $this->assertEquals('body', $message->getBody());
        $this->assertEquals([
            'foo' => 'bar',
            'bar' => 'baz'
        ], $message->getHeaders());
        $this->assertEquals('bar', $message->getHeader('foo'));
        $this->assertNull($message->getHeader('unknow-header'));
    }

    public function testAction()
    {
        $message = new Spike('foo', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $message->setAction('bar');
        $this->assertEquals('bar', $message->getAction());
    }

    public function testHeader()
    {
        $message = new Spike('foo', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $message->setHeader('foo', 'baz');
        $this->assertEquals('baz', $message->getHeader('foo'));
        $this->assertEquals([
            'foo' => 'baz',
            'bar' => 'baz'
        ], $message->getHeaders());
        $message->setHeaders([
            'bar' => 'baz'
        ]);
        $this->assertEquals([
            'bar' => 'baz'
        ], $message->getHeaders());
    }

    public function testToString()
    {
        $message = new Spike('foo', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $version = SpikeInterface::VERSION;
        $expected = <<<EOT
Spike-Action: foo\r\nSpike-Version: {$version}\r\nContent-Length: 6\r\nfoo: bar\r\nbar: baz\r\n\r\n"body"
EOT;
        $this->assertEquals($expected, $message->toString());
        $this->assertEquals($expected, (string)$message);
    }

    public function testFromString()
    {
        $version = SpikeInterface::VERSION;
        $string = <<<EOT
Spike-Action: foo\r\nSpike-Version: {$version}\r\nContent-Length: 6\r\nfoo: bar\r\nbar: baz\r\n\r\n"body"
EOT;
        $message = Spike::fromString($string);
        $this->assertEquals('foo', $message->getAction());
        $this->assertEquals('body', $message->getBody());
        $this->assertEquals([
            'Spike-Action' => 'foo',
            'Spike-Version' => '1',
            'Content-Length' => '6',
            'foo' => 'bar',
            'bar' => 'baz'
        ], $message->getHeaders());
        $this->assertEquals('bar', $message->getHeader('foo'));
        $this->assertNull($message->getHeader('unknow-header'));
    }
}