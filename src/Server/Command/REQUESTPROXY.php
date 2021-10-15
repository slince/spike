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

use Spike\Command\FallbackCommand;
use Spike\Command\CommandInterface;
use Spike\Protocol\Message;

class REQUESTPROXY extends FallbackCommand
{
    public function __construct(int $port)
    {
        parent::__construct(['server_port' => $port]);
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
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('server_port'));
    }
}