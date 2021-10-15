<?php

declare(strict_types=1);

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Command;

use Spike\Exception\BadMessageException;
use Spike\Protocol\Message;

final class CommandFactory
{
    protected $commandMap;

    public function __construct(array $commandMap)
    {
        $this->commandMap = $commandMap;
    }

    /**
     * Creates the command from message.
     *
     * @param Message $message
     * @return CommandInterface
     */
    public function createCommand(Message $message): CommandInterface
    {
        $commandId = $message->getArgument('_cid_');
        if (!isset($this->commandMap[$commandId])) {
            throw new BadMessageException('Cannot find the command id from the message');
        }
        return call_user_func([$this->commandMap[$commandId], 'fromMessage'], $message);
    }
}