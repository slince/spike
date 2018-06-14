<?php
namespace Spike\Tests\Logger;

use Spike\Logger\ConsoleHandler;
use Symfony\Component\Console\Output\StreamOutput;
use Monolog\Formatter\LineFormatter;

class ConsoleHandlerTest extends TestCase
{
    public function testConstruct()
    {
        $stream = fopen('php://memory', 'a+');
        $output = new StreamOutput($stream);
        $handler = new ConsoleHandler($output, 'info');
        $handler->setFormatter(new LineFormatter("%message%"));
        $handler->handle($this->getRecord(200, 'foo'));
        $handler->handle($this->getRecord(200, 'bar'));
        $handler->handle($this->getRecord(200, 'baz'));
        fseek($stream, 0);
        $this->assertEquals('foobarbaz', stream_get_contents($stream));
    }
}