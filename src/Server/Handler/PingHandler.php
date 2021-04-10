<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Handler;

use Spike\Io\Message;

class PingHandler extends AuthAwareHandler
{
    /**
     * @inheritDoc
     */
    public function supports(Message $message)
    {
        return 'ping' === $message->getAction();
    }
}