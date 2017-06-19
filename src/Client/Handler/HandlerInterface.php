<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Handler;

use Psr\Http\Message\RequestInterface;
use Spike\Protocol\SpikeInterface;

interface HandlerInterface
{
    /**
     * Handling the message
     * @param SpikeInterface $message
     */
    public function handle(SpikeInterface $message);
}