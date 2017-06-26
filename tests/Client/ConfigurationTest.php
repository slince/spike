<?php
namespace Spike\Tests\Client;

use PHPUnit\Framework\TestCase;
use Spike\Client\Configuration;

class ConfigurationTest extends TestCase
{
    public function testConstruct()
    {
        $configuration = new Configuration();
        $this->assertEquals('127.0.0.1:8088', $configuration->getServerAddress());
        $this->assertContains('spike.json', $configuration->getDefaultConfigFile());
    }
}