<?php


namespace Spike\Server;


class Configuration
{
    /**
     * @var string
     */
    protected $address;

    /**
     * @var int
     */
    protected $maxWorkers = 4;

    /**
     * @var array
     */
    protected $log = [
        'file' => './server.log',
        'level' => 'info'
    ];

    /**
     * @var array
     */
    protected $users = [
        [
            'username' => 'admin',
            'password' => 'admin'
        ]
    ];

    public function __construct(string $address = '0.0.0.0:8090')
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
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
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @param array $users
     */
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }
}