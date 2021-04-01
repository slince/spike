<?php


namespace Spike\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends ServerCommand
{
    protected function configure()
    {
        $this->setName('serve')
            ->setDescription('Create a spike server.')
            ->addArgument('address', InputArgument::REQUIRED)
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'The configuration file, support json,ini,xml and yaml format.')
            ->addOption('daemon', 'd', InputOption::VALUE_NONE, 'Daemon');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->server->configure();
    }
}