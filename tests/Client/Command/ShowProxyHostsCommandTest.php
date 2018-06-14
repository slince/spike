<?php
namespace Spike\Tests\Client\Command;

use Spike\Client\Client;
use Spike\Client\Command\ShowProxyHostsCommand;
use Symfony\Component\Console\Tester\CommandTester;
use Spike\Tests\TestCase;

class ShowProxyHostsCommandTest extends TestCase
{
    protected function getApplicationMock()
    {
        return $this->getMockBuilder(Client::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testExecute()
    {
        $command = new ShowProxyHostsCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        $this->assertContains('Proxy Host', $commandTester->getDisplay());
    }
}