<?php
namespace Spike\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spike\Exception\InvalidArgumentException;
use Spike\Utility;

class UtilityTest extends PHPUnitTestCase
{
    public function testParseAddress()
    {
        $parts = Utility::parseAddress('127.0.0.1:80');
        $this->assertEquals('127.0.0.1', $parts[0]);
        $this->assertEquals('80', $parts[1]);

        $parts = Utility::parseAddress('  127.0.0.1  :  80 ');
        $this->assertEquals('127.0.0.1', $parts[0]);
        $this->assertEquals('80', $parts[1]);

        $parts = Utility::parseAddress('  127.0.0.1  :  80 :87');
        $this->assertEquals('127.0.0.1', $parts[0]);
        $this->assertEquals('80', $parts[1]);
        $this->assertCount(2, $parts);

        $this->expectException(InvalidArgumentException::class);
        Utility::parseAddress('127.0.0.1');
    }
}