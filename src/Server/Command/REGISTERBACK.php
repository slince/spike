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

class REGISTERBACK implements CommandInterface
{
    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $clientId;

    public function __construct(string $status, string $clientId = null)
    {
        $this->status = $status;
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
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
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'status' => $this->status,
            'client_id' => $this->clientId
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('status'), $message->getArgument('client_id'));
    }
}