<?php
namespace Spike\Tests\Common\Protocol;

use PHPUnit\Framework\TestCase;
use Spike\Common\Exception\BadRequestException;
use Spike\Common\Protocol\Spike;
use Spike\Common\Protocol\SpikeInterface;

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

    public function testGlobalHeader()
    {
        $message = new Spike('foo', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $message2 = new Spike('bar', 'body', [
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
        $this->assertNotContains('Global', $message->toString());
        $this->assertNotContains('Global', $message->toString());
        Spike::setGlobalHeader('Global', 'foo');
        $this->assertContains('Global', $message->toString());
        $this->assertContains('Global', $message2->toString());
    }
}