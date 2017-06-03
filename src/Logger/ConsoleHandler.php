<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Logger;

use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Monolog\Formatter\LineFormatter;

class ConsoleHandler extends StreamHandler
{
    public function __construct(ConsoleOutput $output, $level)
    {
        parent::__construct($output->getStream(), $level);
        $this->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
    }
}