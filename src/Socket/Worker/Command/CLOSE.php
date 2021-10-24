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

namespace Spike\Socket\Worker\Command;

use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class CLOSE implements CommandInterface
{
    /**
     * Close gracefully
     * @var bool
     */
    protected $grace = false;

    public function __construct(bool $grace)
    {
        $this->grace = $grace;
    }

    /**
     * @return bool
     */
    public function isGrace(): bool
    {
        return $this->grace;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommandId(): string
    {
        return 'CLOSE';
    }

    /**
     * {@inheritdoc}
     */
    public function createMessage(): Message
    {
        return new Message(Message::PAYLOAD_CONTROL, [
            'grace' => $this->grace
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('grace'));
    }
}