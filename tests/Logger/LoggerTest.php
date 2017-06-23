<?php
namespace Spike\Tests\Logger;

use Spike\Logger\Logger;
use Symfony\Component\Console\Output\StreamOutput;

class LoggerTest extends TestCase
{
    public function testConstruct()
    {
        $file = tempnam(sys_get_temp_dir(), 'tmp_');
        $stream = fopen('php://memory', 'a+');
        $output = new StreamOutput($stream);
        $logger = new Logger(200, $file, $output);
        $this->assertEquals(200, $logger->getLevel());
        $this->assertCount(2, $logger->getHandlers());
    }
}