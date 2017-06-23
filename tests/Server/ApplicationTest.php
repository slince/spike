<?php
namespace Spike\Tests\Server;

use Slince\Event\SubscriberInterface;
use Spike\Server\Application;
use Spike\Server\Configuration;
use Spike\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function testConstruct()
    {
        $application = new Application(new Configuration());
        $this->assertGreaterThan(0, $application->getDefaultCommands());
        $this->assertGreaterThan(0, $application->getSubscribers());
        $this->assertGreaterThanOrEqual(0, $application->getEvents());
        $this->assertInstanceOf(SubscriberInterface::class, $application);
    }
}