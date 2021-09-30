<?php


namespace Spike\Console\Command;

use Slince\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    const SPIKE_FILE = __DIR__.'../../resources/spike-template.json';
    const SPIKED_FILE = __DIR__.'../../resources/spiked-template.json';

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
        if ($input->getOption('server')) {
            $templateConfigFile = static::SPIKED_FILE;
        } else {
            $templateConfigFile = static::SPIKE_FILE;
        }
        $config = new Config($templateConfigFile);
        $dstPath = $input->getOption('dir');
        $extension = $input->getOption('format');
        if (!in_array($extension, $this->getSupportedFormats())) {
            $output->writeln(sprintf('<error>The format "%s" is not supported</error>', $extension));

            return false;
        }
        if (!$config->dump("{$dstPath}/spike.{$extension}")) {
            $output->writeln('Can not create the configuration file');
        }
        return 0;
    }

    /**
     * Gets all supported formats.
     *
     * @return array
     */
    protected function getSupportedFormats()
    {
        return [
            'json', 'yaml', 'xml',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
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
            new InputOption('server', 's', InputOption::VALUE_NONE,
                'Create configuration file for server'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED,
                'The configuration file format, support json,ini,xml and yaml', 'json'),
            new InputOption('dir', null, InputOption::VALUE_REQUIRED,
                'The directory', getcwd()),
        ));
    }
}