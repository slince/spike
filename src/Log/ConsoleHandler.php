<?php

namespace Spike\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandler extends AbstractProcessingHandler
{
    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct(OutputInterface $output, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->output = $output;
        parent::__construct($level, $bubble);
    }

    /**
     * @inheritDoc
     */
    protected function write(array $record): void
    {
        $this->output->write((string) $record['formatted']);
    }
}