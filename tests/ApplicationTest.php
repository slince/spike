<?php
namespace Spike\Tests;

use Slince\Event\Dispatcher;
use Spike\Application;
use Spike\Configuration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class ApplicationTest extends PHPUnitTestCase
{
    public function testGetter()
    {
        $application = new Application(new Configuration());
        $this->assertInstanceOf(Configuration::class, $application->getConfiguration());
        $this->assertInstanceOf(Dispatcher::class, $application->getDispatcher());

        $this->assertNull($application->getInput());
        $this->assertNull($application->getOutput());
    }
}