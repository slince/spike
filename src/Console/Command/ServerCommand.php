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

namespace Spike\Console\Command;

use Spike\Application;
use Spike\Server\Configuration;
use Spike\Server\Server;
use Symfony\Component\Console\Command\Command;

class ServerCommand extends Command
{
    /**
     * @var Server
     */
    protected $server;

    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param Configuration $configuration
     * @return Server
     */
    protected function getServer(Configuration $configuration): Server
    {
        if (null === $this->server) {
            $this->server = new Server($configuration);
        }
        return $this->server;
    }
}