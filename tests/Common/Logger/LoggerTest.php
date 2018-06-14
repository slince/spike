<?php
namespace Spike\Tests\Common\Logger;

use Spike\Common\Logger\Logger;

class LoggerTest extends TestCase
{
    public function testConstruct()
    {
        $logger = $this->getLoggerStub();
        $this->assertInstanceOf(Logger::class, $logger);
    }
}