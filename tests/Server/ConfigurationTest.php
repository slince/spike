<?php
namespace Spike\Tests\Server;

use PHPUnit\Framework\TestCase;
use Spike\Authentication\PasswordAuthentication;
use Spike\Server\Configuration;

class ConfigurationTest extends TestCase
{
    public function testConstruct()
    {
        $configuration = new Configuration();
        $this->assertEquals('127.0.0.1:8088', $configuration->getAddress());
        $this->assertContains('spiked.json', $configuration->getDefaultConfigFile());
        $this->assertInstanceOf(PasswordAuthentication::class, $configuration->getAuthentication());
    }
}