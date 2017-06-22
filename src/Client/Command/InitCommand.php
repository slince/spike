<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Command;

use Slince\Config\Config;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();
        $this->setName('init')
            ->setDefinition($this->createDefinition())
            ->setDescription('Create a configuration file in the specified directory');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templateConfigFile = __DIR__ . '/../../../spike.json';
        $config = new Config($templateConfigFile);
        $dstPath = $input->getOption('dir');
        $extension = $input->getOption('format');
        if (!$config->dump("{$dstPath}/spike.{$extension}")) {
            $output->writeln("Can not create the configuration file");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption('format', null, InputOption::VALUE_OPTIONAL,
                'The configuration file format, support json,ini,xml and yaml', 'json'),
            new InputOption('dir', null, InputOption::VALUE_OPTIONAL,
                'The directory', getcwd()),
        ));
    }
}