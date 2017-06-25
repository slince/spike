<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Logger;

use Monolog\Logger as Monologer;
use Symfony\Component\Console\Output\OutputInterface;

class Logger extends Monologer
{
    /**
     * The log file
     * @var string
     */
    protected $file;

    /**
     * The log level
     * @var int|string
     */
    protected $level;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct($level, $file, OutputInterface $output)
    {
        $this->level = $level;
        $this->file = $file;;
        $this->output = $output;
        parent::__construct('', $this->createHandlers());
    }

    protected function createHandlers()
    {
        return [
            new FileHandler($this->file, $this->level),
            new ConsoleHandler($this->output, $this->level),
        ];
    }

    /**
     * Gets the log level
     * @return int|string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}