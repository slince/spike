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
use React\Socket\ConnectionInterface as RawConnection;
use React\Stream\Util;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class WrapperConnection extends EventEmitter implements ConnectionInterface
{
    /**
     * @var RawConnection
     */
    protected $stream;

    public function __construct(RawConnection $stream)
    {
        $this->stream = $stream;
        Util::forwardEvents($this->stream, $this, ['data', 'end', 'error', 'close', 'drain']);
    }

    /**
     * Returns the full remote address (URI) where this connection has been established with.
     *
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return $this->stream->getRemoteAddress();
    }

    /**
     * Returns the full local address (full URI with scheme, IP and port) where this connection has been established with.
     *
     * @return string
     */
    public function getLocalAddress(): string
    {
        return $this->stream->getLocalAddress();
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect(bool $force = false)
    {
        $force ? $this->stream->close() : $this->stream->end();
    }

    /**
     * {@inheritdoc}
     */
    public function executeCommand(CommandInterface $command)
    {
        $message = $command->createMessage();
        $message->addArgument('_cid_', $command->getCommandId());
        $message = Message::pack($message);
        $this->stream->write($message);
    }

    /**
     * @return RawConnection
     */
    public function getStream(): RawConnection
    {
        return $this->stream;
    }
}