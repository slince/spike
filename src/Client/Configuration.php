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


namespace Spike\Client;


class Configuration
{
    /**
     * @var string
     */
    protected $serverAddress;

    /**
     * @var int
     */
    protected $maxWorkers = 4;

    /**
     * @var Tunnel[]
     */
    protected $tunnels;

    /**
     * @var array
     */
    protected $log = [
        'file' => './client.log',
        'level' => 'info'
    ];

    /**
     * @var array
     */
    protected $user = [
        'username' => 'admin',
        'password' => 'admin'
    ];

    public function __construct(string $serverAddress = '127.0.0.1:8090')
    {
        $this->serverAddress = $serverAddress;
    }

    /**
     * @return string
     */
    public function getServerAddress(): string
    {
        return $this->serverAddress;
    }

    /**
     * @param string $serverAddress
     */
    public function setServerAddress(string $serverAddress)
    {
        $this->serverAddress = $serverAddress;
    }

    /**
     * @return int
     */
    public function getMaxWorkers(): int
    {
        return $this->maxWorkers;
    }

    /**
     * @param int $maxWorkers
     */
    public function setMaxWorkers(int $maxWorkers): void
    {
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @return array
     */
    public function getLog(): array
    {
        return $this->log;
    }

    /**
     * @param array $log
     */
    public function setLog(array $log): void
    {
        $this->log = $log;
    }

    /**
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @param array $user
     */
    public function setUser(array $user)
    {
        $this->user = $user;
    }

    /**
     * @param Tunnel[] $tunnels
     */
    public function setTunnels(array $tunnels): void
    {
        $this->tunnels = $tunnels;
    }

    /**
     * @return Tunnel[]
     */
    public function getTunnels(): array
    {
        return $this->tunnels;
    }

    public function addTunnel(Tunnel $tunnel)
    {
        $this->tunnels[] = $tunnel;
    }
}