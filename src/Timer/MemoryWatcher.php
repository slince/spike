<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Timer;

use Spike\Logger\Logger;

class MemoryWatcher extends PeriodicTimer
{
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke()
    {
        $this->logger->info(sprintf('Memory usage: %s', memory_get_usage()));
    }

    public function getInterval()
    {
        return 5;
    }
}