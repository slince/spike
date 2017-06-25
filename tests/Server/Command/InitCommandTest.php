<?php
namespace Spike\Tests\Server\Command;

use PHPUnit\Framework\TestCase;
use Spike\Server\Command\InitCommand;
use Spike\Server\Application;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends TestCase
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
        $command = new InitCommand($this->getApplicationMock());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();
        $commandTester->execute([
            '--dir' => $dir
        ]);
        $this->assertFileExists("{$dir}/spiked.json");
    }

    public function testFormatYaml()
    {
        $command = new InitCommand($this->getApplicationMock());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();

        if (!class_exists('Symfony\Component\Yaml\Yaml')) {
            $this->markTestSkipped();
        }
        $commandTester->execute([
            '--dir' => $dir,
            '--format' => 'yaml'
        ]);
        $this->assertFileExists("{$dir}/spiked.yaml");
    }

    public function testFormatXml()
    {
        $command = new InitCommand($this->getApplicationMock());
        $commandTester = new CommandTester($command);
        $dir = sys_get_temp_dir();

        if (!class_exists('LSS\Array2XML')) {
            $this->markTestSkipped();
        }
        $commandTester->execute([
            '--dir' => $dir,
            '--format' => 'xml'
        ]);
        $this->assertFileExists("{$dir}/spiked.xml");
    }
}