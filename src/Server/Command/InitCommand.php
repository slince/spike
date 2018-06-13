<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Spike\Server\Command;

use Slince\Config\Config;
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
        $templateConfigFile = __DIR__ . '/../../../spiked-template.json';
        $config = new Config($templateConfigFile);
        $dstPath = $input->getOption('dir');
        $extension = $input->getOption('format');
        if (!in_array($extension, $this->getSupportedFormats())) {
            $output->writeln(sprintf('<error>The format "%s" is not supported</error>', $extension));
            return false;
        }
        if (!$config->dump("{$dstPath}/spiked.{$extension}")) {
            $output->writeln("Can not create the configuration file");
        }
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }

    /**
     * Gets all supported formats
     * @return array
     */
    protected function getSupportedFormats()
    {
        return [
            'json', 'yaml', 'xml'
        ];
    }

    /**
     * {@inheritdoc}
     */
    private function createDefinition()
    {
        return new InputDefinition(array(
            new InputOption('format', null, InputOption::VALUE_REQUIRED,
                'The configuration file format, support json,ini,xml and yaml', 'json'),
            new InputOption('dir', null, InputOption::VALUE_REQUIRED,
                'The directory', getcwd()),
        ));
    }
}