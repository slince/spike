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
        $parser->push($message);
        $this->assertEquals($message, $parser->parseFirst());
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
        $parser->push($message);
        $this->assertEquals($expected, $parser->parseFirst());
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
        $parser->push($message1);
        $this->assertEquals(null, $parser->parseFirst());
        $this->assertEquals($message1, $parser->getRemainingChunk());
        $parser->push($message2);

        $expected = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $this->assertEquals($expected, $parser->parseFirst());
        $this->assertEquals($expected, $parser->getRemainingChunk());
    }

    public function testParseMany()
    {
        $message1 = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $parser = new HttpHeaderParser();
        $parser->push($message1);
        $parser->push($message1);
        $parser->push('Spike');
        $messages = $parser->parse();
        $this->assertCount(2, $messages);
        $this->assertEquals($message1, $messages[0]);
        $this->assertEquals($message1, $messages[1]);
        $this->assertEquals('Spike', $parser->getRemainingChunk());
    }
}