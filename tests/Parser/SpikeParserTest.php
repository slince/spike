<?php
namespace Spike\Tests\Parser;

use PHPUnit\Framework\TestCase;
use Spike\Exception\InvalidArgumentException;
use Spike\Parser\SpikeParser;

class SpikeParserTest extends TestCase
{
    public function testPushIncoming()
    {
        $parser = new SpikeParser();
        $this->assertEquals('', $parser->getRestData());
        $parser->pushIncoming('foo');
        $this->assertEquals('foo', $parser->getRestData());
    }

    public function testParse()
    {
        $message = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbody
EOT;

        $parser = new SpikeParser();
        $parser->pushIncoming($message);
        $this->assertEquals($message, $parser->parseFirst());
    }

    public function testParseBadMessage()
    {
        $message = <<<EOT
Spike-Action: start_proxy\r\n\r\nbody
EOT;

        $parser = new SpikeParser();
        $parser->pushIncoming($message);
        $this->expectException(InvalidArgumentException::class);
        $this->assertEquals($message, $parser->parseFirst());
    }

    public function testParseWithRest()
    {
        $message = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbodyhello world
EOT;

        $expected = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbody
EOT;
        $parser = new SpikeParser();
        $parser->pushIncoming($message);
        $this->assertEquals($expected, $parser->parseFirst());
        $this->assertEquals('hello world', $parser->getRestData());
    }

    public function testParseChunk()
    {
        $message1 = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\n
EOT;

        $message2 = <<<EOT
bodySpike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbody
EOT;
        $parser = new SpikeParser();
        $parser->pushIncoming($message1);
        $this->assertEquals(null, $parser->parseFirst());
        $this->assertEquals($message1, $parser->getRestData());
        $parser->pushIncoming($message2);

        $expected = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbody
EOT;
        $this->assertEquals($expected, $parser->parseFirst());
        $this->assertEquals($expected, $parser->getRestData());
    }

    public function testParseMany()
    {
        $message1 = <<<EOT
Spike-Action: start_proxy\r\nContent-Length: 4\r\n\r\nbody
EOT;
        $parser = new SpikeParser();
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