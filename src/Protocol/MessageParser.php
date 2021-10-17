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


namespace Spike\Protocol;

use Spike\Connection\ConnectionInterface;

final class MessageParser
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function parse()
    {
        $buffer = '';
        $readSize = 0;
        $meta = null;
        $this->connection->on('data', function($data) use(&$buffer, &$readSize, &$meta){
            $buffer .= $data;
            $readSize += strlen($data);
            if (null === $meta && $readSize >= Message::HEADER_SIZE) {
                $meta = Message::parseHeader(substr($buffer, 0, Message::HEADER_SIZE));
                $this->connection->emit('meta', $meta);
                $buffer = substr($buffer, Message::HEADER_SIZE); // reset buffer
                $readSize = strlen($buffer);
            }
            if (null !== $meta && $readSize >= $meta['size']) {
                $body = substr($buffer, 0, $meta['size']);
                $payload = Message::parsePayload($body);
                $message = new Message($meta['flags'], $payload, $body);
                $this->connection->emit('message', [$message, $this->connection, $meta]);
                $buffer = substr($buffer, $meta['size']); // reset buffer
                $readSize = strlen($buffer);
                $meta = null;

                // maybe buffer contains 2+ message.
                if ($readSize >= Message::HEADER_SIZE) {
                    $this->connection->emit('data', ['']);
                }
            }
        });
    }
}