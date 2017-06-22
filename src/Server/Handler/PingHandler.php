<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\SpikeInterface;

class PingHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        $this->client->setLastActiveAt(microtime(true));
    }
}