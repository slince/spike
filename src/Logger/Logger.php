<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Logger;

use Monolog\Logger as Monologer;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * @var ConsoleOutput
     */
    protected $output;

    public function __construct($level, $file, ConsoleOutput $output)
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
}