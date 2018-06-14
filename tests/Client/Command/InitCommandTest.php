<?php
namespace Spike\Tests\Client\Command;

use Spike\Tests\TestCase;
use Spike\Client\Command\InitCommand;
use Spike\Client\Client;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TestCase
{

    protected function getApplicationMock()
    {
        return $this->getMockBuilder(Client::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testClient()
    {
        $command = new InitCommand($this->getClientStub());
        $this->assertInstanceOf(Client::class, $command->getClient());
    }

    public function testExecute()
    {
        $command = new InitCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();
        $commandTester->execute([
            '--dir' => $dir
        ]);
        $this->assertFileExists("{$dir}/spike.json");
    }

    public function testExecuteUnsupportedFormat()
    {
        $command = new InitCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();
        $commandTester->execute([
            '--dir' => $dir,
            '--format' => 'foo'
        ]);
        $this->assertContains('The format "foo" is not supported', $commandTester->getDisplay());
    }

    public function testExecuteDumpError()
    {
        $command = new InitCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--dir' => preg_match('/win/i', PHP_OS) ? 'foo://a/' :  '/dev/null'
        ]);
        $this->assertContains('Can not create the configuration file', $commandTester->getDisplay());
    }

    public function testFormatYaml()
    {
        $command = new InitCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();

        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped();
        }
        $commandTester->execute([
            '--dir' => $dir,
            '--format' => 'yaml'
        ]);
        $this->assertFileExists("{$dir}/spike.yaml");
    }

    public function testFormatXml()
    {
        $command = new InitCommand($this->getClientStub());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();

        if (!class_exists('LSS\Array2XML')) {
            $this->markTestSkipped();
        }
        $commandTester->execute([
            '--dir' => $dir,
            '--format' => 'xml'
        ]);
        $this->assertFileExists("{$dir}/spike.xml");
    }
}