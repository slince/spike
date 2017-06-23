<?php
namespace Spike\Tests\Server\Command;

use PHPUnit\Framework\TestCase;
use Spike\Server\Application;
use Spike\Server\Command\SpikeCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SpikeCommandTest extends TestCase
{
    protected function getApplicationMock()
    {
        return $this->getMockBuilder(Application::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $command = new SpikeCommand($this->getApplicationMock());
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $this->assertContains('spiked', $commandTester->getDisplay());
        $this->assertContains('Spike is a reverse proxy', $commandTester->getDisplay());
    }
}