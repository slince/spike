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

use Spike\Server\Configuration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ServeCommand extends ServerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('serve')
            ->setDescription('Create a spike server.')
            ->addArgument('address', InputArgument::OPTIONAL)
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The configuration file, support json,ini,xml and yaml format.')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, 'Daemon');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->createConfiguration($input);
        $server = $this->getServer($configuration);
        $server->configure([
            'address' => $configuration->getAddress(),
            'max_workers' => $configuration->getMaxWorkers()
        ]);
        $server->serve();
        return 0;
    }

    protected function createConfiguration(InputInterface $input): Configuration
    {
        if ($configFile = $input->getOption('config')) {
            if (!file_exists($configFile)) {
                throw new \RuntimeException(sprintf('The config file "%s" is not exists', $configFile));
            }
            $configuration = $this->getApplication()->getSerializer()->deserialize(
                file_get_contents($configFile),
                Configuration::class,
                pathinfo($configFile, PATHINFO_EXTENSION)
            );
        } elseif ($address = $input->getArgument('address')) {
            $configuration = new Configuration($address);
        } else {
            throw new \RuntimeException('Either --config or --address must be provided');
        }
        return $configuration;
    }
}