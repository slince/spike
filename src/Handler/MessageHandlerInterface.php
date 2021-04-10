<?php


namespace Spike\Handler;

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

    /**
     * Returns whether this class supports the given message.
     * @param Message $message
     * @return bool
     */
    public function supports(Message $message);
}