<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Command;

use Spike\Server\ServerInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var ServerInterface
     */
    protected $server;

    public function __construct(ServerInterface $server)
    {
        $this->server = $server;
        parent::__construct(null);
    }

    /**
     * @return ServerInterface
     */
    public function getServer()
    {
        return $this->server;
    }
}