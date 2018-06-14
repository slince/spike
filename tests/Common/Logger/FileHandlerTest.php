<?php
namespace Spike\Tests\Common\Logger;

use Spike\Common\Logger\FileHandler;
use Symfony\Component\Console\Output\StreamOutput;
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
        $this->assertEquals('foobarbaz', file_get_contents($file));
        @unlink($file);
    }
}