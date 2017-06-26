<?php
namespace Spike\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spike\Configuration;

class ConfigurationTest extends PHPUnitTestCase
{
    public function testGetter()
    {
        $configuration = new Configuration();
        $this->assertEquals('Asia/shanghai', $configuration->getTimezone());
        $this->assertEquals( getcwd() . '/access.log', $configuration->getLogFile());
        $this->assertEquals( 'info', $configuration->getLogLevel());

        $configuration->load(__DIR__ . '/Fixtures/base-config.json');
        $this->assertEquals('Asia/prc', $configuration->getTimezone());
        $this->assertEquals( '/access.log', $configuration->getLogFile());
        $this->assertEquals( 'warning', $configuration->getLogLevel());
    }
}