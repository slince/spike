<?php
namespace Spike\Tests\Common\Logger;

use Spike\Common\Logger\ConsoleHandler;
use Symfony\Component\Console\Output\StreamOutput;
use Monolog\Formatter\LineFormatter;

class ConsoleHandlerTest extends TestCase
{
    public function testConstruct()
    {
        $stream = fopen('php://memory', 'a+');
        $output = new StreamOutput($stream);
        $handler = new ConsoleHandler($this->getEventLoop(), $output, 'info');
        $handler->setFormatter(new LineFormatter("%message%"));
        $handler->handle($this->getRecord(200, 'foo'));
        $handler->handle($this->getRecord(200, 'bar'));
        $handler->handle($this->getRecord(200, 'baz'));

        $this->getEventLoop()->addWriteStream($stream, function($stream){
            fseek($stream, 0);
            $this->assertEquals('foobarbaz', stream_get_contents($stream));
            $this->getEventLoop()->removeWriteStream($stream);
        });
        $this->assertEquals('', stream_get_contents($stream));
    }
}