<?php
namespace Spike\Tests\Client\Command;

use Spike\Client\Application;
use Spike\Client\Command\ShowProxyHostsCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Spike\Tests\TestCase;

class ShowProxyHostsCommandTest extends TestCase
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
        $application = $this->getApplicationMock();
        $application->setKernel($this->getClientStub());
        $command = new ShowProxyHostsCommand($application);
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $this->assertContains('Proxy Host', $commandTester->getDisplay());
    }
}