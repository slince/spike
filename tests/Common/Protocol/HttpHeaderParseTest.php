<?php
namespace Spike\Tests\Common\Protocol;

use PHPUnit\Framework\TestCase;
use Spike\Common\Protocol\HttpHeaderParser;

class HttpHeaderParseTest extends TestCase
{
    public function testPushIncoming()
    {
        $parser = new HttpHeaderParser();
        $this->assertEquals('', $parser->getRemainingChunk());
        $parser->push('foo');
        $this->assertEquals('foo', $parser->getRemainingChunk());
    }

    public function testParse()
    {
        $message = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;

        $parser = new HttpHeaderParser();
        $this->assertEquals($message, $parser->push($message)[0]);
    }

    public function testParseWithRest()
    {
        $message = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\nhello world
EOT;

        $expected = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $parser = new HttpHeaderParser();
        $this->assertEquals($expected, $parser->push($message)[0]);
        $this->assertEquals('hello world', $parser->getRemainingChunk());
    }

    public function testParseChunk()
    {
        $message1 = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\n
EOT;

        $message2 = <<<EOT
Host: www.foo.com\r\n\r\nGET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $parser = new HttpHeaderParser();
        $this->assertEquals([], $parser->push($message1));
        $this->assertEquals($message1, $parser->getRemainingChunk());

        $expected = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $this->assertEquals($expected,  $parser->push($message2)[0]);
        $this->assertEquals('', $parser->getRemainingChunk());
    }
}