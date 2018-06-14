<?php
namespace Spike\Tests\Client;

use PHPUnit\Framework\TestCase;
use Spike\Client\Configuration;

class ConfigurationTest extends TestCase
{
    public function testConstruct()
    {
        $configuration = new Configuration();
        $this->assertEquals('127.0.0.1:8090', $configuration->getServerAddress());
        $this->assertContains('spike.json', $configuration->getDefaultConfigFile());
    }
    public function testGetter()
    {
        $configuration = new Configuration();
        $this->assertEquals('Asia/shanghai', $configuration->getTimezone());
        $this->assertEquals( getcwd() . '/access.log', $configuration->getLogFile());
        $this->assertEquals( 'info', $configuration->getLogLevel());

        $configuration->load(__DIR__ . '/../Fixtures/base-config.json');
        $this->assertEquals('Asia/prc', $configuration->getTimezone());
        $this->assertEquals( '/access.log', $configuration->getLogFile());
        $this->assertEquals( 'warning', $configuration->getLogLevel());
    }
}