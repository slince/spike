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

namespace Spike\Client\Command;

use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REGISTERPROXY implements CommandInterface
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var int
     */
    protected $serverPort;

    public function __construct(string $clientId, int $serverPort)
    {
        $this->clientId = $clientId;
        $this->serverPort = $serverPort;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return int
     */
    public function getServerPort(): int
    {
        return $this->serverPort;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'REGISTERPROXY';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'client_id' => $this->clientId,
            'server_port' => $this->serverPort
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static(
            $message->getArgument('server_port'),
            $message->getArgument('client_id')
        );
    }
}