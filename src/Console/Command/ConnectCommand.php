<?php

namespace Spike\Console\Command;

use Monolog\Logger;
use Spike\Client\Client;
use Spike\Client\Configuration;
use Spike\Console\Utils;
use Symfony\Component\Console\Command\Command;

class ConnectCommand extends Command
{
    /**
     * @var Client
     */
    protected $client;

    public function getClient(Configuration $configuration)
    {
        if (null === $this->client) {
            $this->client = new Client($configuration);
        }
    }

    protected function createLogger(Configuration $configuration)
    {
        $fileLog = $configuration->getLog();
        $logger = new Logger('spike');
        $logger->pushHandler(Utils::createLogFileHandler($fileLog['file'], $fileLog['level']));
    }
}