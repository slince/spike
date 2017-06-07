<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Tunnel;

use React\Socket\ConnectionInterface;

interface TunnelInterface
{
    public function open();

    public function isActive();

    public function close();

    public function pipe(ConnectionInterface $connection);

    public function write();

    /**
     * @return ConnectionInterface
     */
    public function getConnection();

    public function getPort();
}