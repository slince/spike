<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Timer;

class SummaryWatcher extends PeriodicTimer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $message = sprintf('Client Total: %s; Tunnel Server: %s',
            count($this->server->getClients()),
            count($this->server->getTunnelServers())
        );
        $this->server->getLogger()->info($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 30;
    }
}