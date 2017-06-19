<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\SpikeInterface;

interface HandlerInterface
{
    /**
     * Handling the message
     * @param SpikeInterface $message
     */
    public function handle(SpikeInterface $message);
}