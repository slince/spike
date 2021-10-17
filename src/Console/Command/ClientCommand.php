<?php

namespace Spike\Console\Command;

use Monolog\Logger;
use React\EventLoop\LoopInterface;
use Spike\Client\Client;
use Spike\Client\Configuration;
use Spike\Console\Utils;
use Spike\Log\ConsoleHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClientCommand extends Command
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
        parent::__construct();
    }

    /**
     * Create a specific spike client.
     *
     * @param Configuration $configuration
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Client
     */
    public function createClient(Configuration $configuration, InputInterface $input, OutputInterface $output): Client
    {
        if (null === $this->client) {
            $logger = $this->createLogger($configuration, $input, $output);
            $this->client = new Client($configuration, $logger, $this->loop);
        }
        return $this->client;
    }

    protected function createLogger(Configuration $configuration, InputInterface $input, OutputInterface $output): Logger
    {
        $log = $configuration->getLog();
        $logger = new Logger('spike');
        $logger->pushHandler(Utils::createLogFileHandler($log['file'], $log['level'], $this->loop));
        $console = $configuration->getConsole();
        if (!$input->getOption('quiet') && $console['enabled']) {
            $logger->pushHandler(new ConsoleHandler($output, $console['level']));
        }
        return $logger;
    }
}