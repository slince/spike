<?php

namespace Spike\Server\Command;

use Spike\Command\FallbackCommand;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REGISTERBACK extends FallbackCommand
{
    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';

    public function __construct(string $status, string $clientId = null)
    {
        parent::__construct(['status' => $status, 'client_id' => $clientId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REGISTERBACK';
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('status'), $message->getArgument('client_id'));
    }
}