<?php
namespace Spike\Tests\Logger;

use Symfony\Component\Console\Output\OutputInterface;

class LoggerTest extends TestCase
{
    public function testConstruct()
    {
        $logger = $this->getLoggerStub();
        $this->assertEquals(200, $logger->getLevel());
        $this->assertCount(2, $logger->getHandlers());
        $this->assertInstanceOf(OutputInterface::class, $logger->getOutput());
    }
}