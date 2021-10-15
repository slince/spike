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

class REGISTERBACK extends FallbackCommand
{
    const STATUS_OK = 'ok';
    const STATUS_FAIL = 'fail';

    public function __construct(string $status, string $clientId = null)
    {
        parent::__construct(['status' => $status, 'client_id' => $clientId]);
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
    public static function fromMessage(Message $message): CommandInterface
    {
        return new static($message->getArgument('status'), $message->getArgument('client_id'));
    }
}