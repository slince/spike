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

use Monolog\Logger;
use React\EventLoop\LoopInterface;
use Spike\Application;
use Spike\Console\Utils;
use Spike\Log\ConsoleHandler;
use Spike\Server\Configuration;
use Spike\Server\Server;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ServerCommand extends Command
{
    /**
     * @var LoopInterface
     */
    protected $loop;

    /**
     * @var Server
     */
    protected $server;

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
        parent::__construct();
    }

    /**
     * @param Configuration $configuration
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Server
     */
    protected function createServer(Configuration $configuration, InputInterface $input, OutputInterface $output): Server
    {
        if (null === $this->server) {
            $logger = $this->createLogger($configuration, $input, $output);
            $this->server = new Server($configuration, $logger, $this->loop);
        }
        return $this->server;
    }


    protected function createLogger(Configuration $configuration, InputInterface $input, OutputInterface $output): Logger
    {
        $log = $configuration->getLog();
        $logger = new Logger('spiked');
        $logger->pushHandler(Utils::createLogFileHandler($log['file'], $log['level'], $this->loop));
        $console = $configuration->getConsole();
        if (!$input->getOption('quiet') && $console['enabled']) {
            $logger->pushHandler(new ConsoleHandler($output, $console['level']));
        }
        return $logger;
    }
}