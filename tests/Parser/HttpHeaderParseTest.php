<?php
namespace Spike\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Spike\Parser\HttpHeaderParser;

class HttpHeaderParseTest extends TestCase
{
    public function testPushIncoming()
    {
        $parser = new HttpHeaderParser();
        $this->assertEquals('', $parser->getRestData());
        $parser->pushIncoming('foo');
        $this->assertEquals('foo', $parser->getRestData());
    }

    public function testParse()
    {
        $message = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;

        $parser = new HttpHeaderParser();
        $parser->pushIncoming($message);
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
        $parser->pushIncoming($message);
        $this->assertEquals($expected, $parser->parseFirst());
        $this->assertEquals('hello world', $parser->getRestData());
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
        $parser->pushIncoming($message1);
        $this->assertEquals(null, $parser->parseFirst());
        $this->assertEquals($message1, $parser->getRestData());
        $parser->pushIncoming($message2);

        $expected = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $this->assertEquals($expected, $parser->parseFirst());
        $this->assertEquals($expected, $parser->getRestData());
    }

    public function testParseMany()
    {
        $message1 = <<<EOT
GET http://www.foo.com/ HTTP/1.1\r\nHost: www.foo.com\r\n\r\n
EOT;
        $parser = new HttpHeaderParser();
        $parser->pushIncoming($message1);
        $parser->pushIncoming($message1);
        $parser->pushIncoming('Spike');
        $messages = $parser->parse();
        $this->assertCount(2, $messages);
        $this->assertEquals($message1, $messages[0]);
        $this->assertEquals($message1, $messages[1]);
        $this->assertEquals('Spike', $parser->getRestData());
    }
}