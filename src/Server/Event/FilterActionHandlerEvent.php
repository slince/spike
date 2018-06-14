<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Spike\Server\Event;

use React\Socket\ConnectionInterface;
use Slince\Event\Event;
use Spike\Common\Protocol\SpikeInterface;
use Spike\Server\Handler\ActionHandlerInterface;


class FilterActionHandlerEvent extends Event
{
    /**
     * @var SpikeInterface
     */
    protected $message;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ActionHandlerInterface
     */
    protected $actionHandler;

    public function __construct($subject, SpikeInterface $message, ConnectionInterface $connection)
    {
        $this->message = $message;
        $this->connection = $connection;
        parent::__construct(Events::SERVER_ACTION, $subject);
    }

    /**
     * @return SpikeInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ActionHandlerInterface $actionHandler
     */
    public function setActionHandler($actionHandler)
    {
        $this->actionHandler = $actionHandler;
    }

    /**
     * @return ActionHandlerInterface
     */
    public function getActionHandler()
    {
        return $this->actionHandler;
    }
}