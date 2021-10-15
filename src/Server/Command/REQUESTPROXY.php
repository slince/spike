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

namespace Spike\Server\Command;

use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REQUESTPROXY implements CommandInterface
{
    /**
     * @var int
     */
    protected $serverPort;

    public function __construct(int $serverPort)
    {
        $this->serverPort = $serverPort;
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
        return 'REQUESTPROXY';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'server_port' => $this->serverPort
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('server_port'));
    }
}