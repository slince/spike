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
    protected $timeout = 2;

    /**
     * @var int
     */
    protected $readTimeout = 3;

    /**
     * @var int
     */
    protected $maxWorkers = 4;

    /**
     * @var TunnelCollection
     */
    protected $tunnels = [];

    /**
     * @var array
     */
    protected $console = [
        'enabled' => true,
        'level' => 'info'
    ];

    /**
     * @var array
     */
    protected $log = [
        'file' => './spike.log',
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
        $this->tunnels = new TunnelCollection();
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
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getReadTimeout(): int
    {
        return $this->readTimeout;
    }

    /**
     * @param int $readTimeout
     */
    public function setReadTimeout(int $readTimeout)
    {
        $this->readTimeout = $readTimeout;
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
    public function setMaxWorkers(int $maxWorkers)
    {
        $this->maxWorkers = $maxWorkers;
    }

    /**
     * @param array $console
     */
    public function setConsole(array $console)
    {
        $this->console = $console;
    }

    /**
     * @return array
     */
    public function getConsole(): array
    {
        return $this->console;
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
    public function setLog(array $log)
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

    public function setTunnels(array $tunnels)
    {
        $tunnelsMap = [];
        foreach ($tunnels as $info) {
            $tunnel = new Tunnel($info['dsn'], $info['server_port']);
            if (isset($tunnel['proxy_pool_size'])) {
                $tunnel->setProxyPoolSize($tunnel['proxy_pool_size']);
            }
            if (isset($tunnel['max_workers'])) {
                $tunnel->setMaxWorkers($tunnel['max_workers']);
            }
            $tunnelsMap[$info['server_port']] = $tunnel;
        }
        $this->tunnels = new TunnelCollection($tunnelsMap);
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