<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Handler;

use Spike\Protocol\MessageInterface;

interface HandlerInterface
{
    /**
     * Handling the message
     * @param MessageInterface $message
     */
    public function handle(MessageInterface $message);
}