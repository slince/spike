<?php

namespace Spike\Console\Command;

use React\EventLoop\LoopInterface;
use Spike\Client\Configuration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConnectCommand extends ClientCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('connect')
            ->setDescription('Connect to a spike server.')
            ->addArgument('serverAddress', InputArgument::OPTIONAL)
            ->addOption('config', 'c', InputOption::VALUE_REQUIRED, 'The configuration file, support json,ini,xml and yaml format.')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, 'Daemon');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configuration = $this->createConfiguration($input);
        $client = $this->createClient($configuration, $input, $output);
        $client->run();
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
            var_dump($configuration->getTunnels());exit;
        } elseif ($serverAddress = $input->getArgument('serverAddress')) {
            $configuration = new Configuration($serverAddress);
        } else {
            throw new \RuntimeException('Either --config or --address must be provided');
        }
        return $configuration;
    }
}