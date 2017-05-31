<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Psr\Http\Message\RequestInterface;
use Spike\Protocol\MessageInterface;

interface HandlerInterface
{
    /**
     * Handling the message
     * @param MessageInterface|RequestInterface $message
     */
    public function handle($message);
}