<?php


namespace Spike\Server\Handler;

use React\Socket\ConnectionInterface;
use Spike\Io\Message;

interface MessageHandlerInterface
{
    /**
     * Handling the message.
     * @param Message $message
     * @param ConnectionInterface $connection
     */
    public function handle(Message $message, ConnectionInterface $connection);
}