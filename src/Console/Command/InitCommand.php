<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Console\Command;

use Slince\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    const RESOURCE_DIR = __DIR__.'../../../../resources/';
    const SPIKE_FILE = self::RESOURCE_DIR  . 'spike.json';
    const SPIKED_FILE = self::RESOURCE_DIR . 'spiked.json';

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
            $template = static::SPIKED_FILE;
        } else {
            $template = static::SPIKE_FILE;
        }
        $config = new Config($template);
        $dstPath = $input->getOption('dir');
        $extension = $input->getOption('format');
        if (!in_array($extension, $this->getSupportedFormats())) {
            $output->writeln(sprintf('<error>The format "%s" is not supported</error>', $extension));

            return false;
        }
        $filename = pathinfo($template, PATHINFO_FILENAME);
        if (!$config->dump("{$dstPath}/{$filename}.{$extension}")) {
            $output->writeln('Can not create the configuration file');
        }
        return 0;
    }

    /**
     * Gets all supported formats.
     *
     * @return array
     */
    protected function getSupportedFormats(): array
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
    public function getNativeDefinition(): InputDefinition
    {
        return $this->createDefinition();
    }

    private function createDefinition(): InputDefinition
    {
        return new InputDefinition(array(
            new InputOption('server', 's', InputOption::VALUE_NONE,
                'Create configuration file for server'),
            new InputOption('format', null, InputOption::VALUE_REQUIRED,
                'The configuration file format, support json,ini,xml and yaml', 'yaml'),
            new InputOption('dir', null, InputOption::VALUE_REQUIRED,
                'The directory', getcwd()),
        ));
    }
}