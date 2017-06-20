<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleHandler extends StreamHandler
{
    public function __construct(OutputInterface $output, $level)
    {
        parent::__construct($output->getStream(), $level);
        $this->setFormatter(new LineFormatter("[%datetime%] %level_name%: %message%\n"));
    }
}