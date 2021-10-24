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

namespace Spike\Connection;

use Evenement\EventEmitter;
use React\Stream\DuplexStreamInterface;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class StreamConnection extends EventEmitter implements ConnectionInterface
{
    /**
     * @var DuplexStreamInterface
     */
    protected $stream;

    public function __construct(DuplexStreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function disconnect(bool $force = false)
    {
        $force ? $this->stream->close() : $this->stream->end();
    }

    public function executeCommand(CommandInterface $command)
    {
        $message = $command->createMessage();
        $message->addArgument('_cid_', $command->getCommandId());
        $message = Message::pack($message);
        $this->stream->write($message);
    }
}