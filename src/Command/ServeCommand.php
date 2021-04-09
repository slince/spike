<?php


namespace Spike\Command;

use Spike\Server\Configuration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ServeCommand extends ServerCommand
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected function configure()
    {
        $this->setName('serve')
            ->setDescription('Create a spike server.')
            ->addArgument('address', InputArgument::OPTIONAL)
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration file, support json,ini,xml and yaml format.')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, 'Daemon');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->createConfiguration($input);
        $this->server->configure([
            'address' => $configuration->getAddress(),
            'max_workers' => $configuration->getMaxWorkers()
        ]);
        $this->server
        return 0;
    }

    protected function createConfiguration(InputInterface $input)
    {
        if ($input->hasOption('config')) {
            $configFile = $input->getOption('config');
            if (!file_exists($configFile)) {
                throw new \RuntimeException(sprintf('The config file "%s" is not exists', $configFile));
            }
            $configuration = $this->getApplication()->getSerializer()->deserialize(
                file_get_contents($configFile),
                Configuration::class,
                pathinfo($configFile, PATHINFO_EXTENSION)
            );
        } elseif ($input->hasArgument('address')) {
            $configuration = new Configuration($input->getArgument('address'));
        } else {
            throw new \RuntimeException('Either --config or --address must be provided');
        }
        return $configuration;
    }
}