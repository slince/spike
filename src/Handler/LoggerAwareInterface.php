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

namespace Spike\Handler;

use Psr\Log\LoggerInterface;

interface LoggerAwareInterface
{
    /**
     * Set logger instance.
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);
}