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

use Spike\Protocol\Message;

interface CommandInterface
{
    /**
     * Gets the command id.
     *
     * @return string
     */
    public function getCommandId(): string;

    /**
     * Create the message instance.
     *
     * @return Message
     */
    public function createMessage(): Message;

    /**
     * Create command base on given message.
     *
     * @param Message $message
     * @return static
     */
    public static function fromMessage(Message $message): CommandInterface;
}