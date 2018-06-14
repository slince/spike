<?php
namespace Spike\Tests\Common\Logger;

use Spike\Common\Logger\FileHandler;
use Monolog\Formatter\LineFormatter;

class FileHandlerTest extends TestCase
{
    public function testConstruct()
    {
        $file = tempnam(sys_get_temp_dir(), 'tmp_');
        $handler = new FileHandler($this->getEventLoop(), $file, 'info');
        $handler->setFormatter(new LineFormatter("%message%"));
        $handler->handle($this->getRecord(200, 'foo'));
        $handler->handle($this->getRecord(200, 'bar'));
        $handler->handle($this->getRecord(200, 'baz'));

        $this->getEventLoop()->addWriteStream($handler->getStream(), function($stream){
            fseek($stream, 0);
            $this->assertEquals('foobarbaz', stream_get_contents($stream));
            $this->getEventLoop()->removeWriteStream($stream);
        });
        @unlink($file);
    }
}