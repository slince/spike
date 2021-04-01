<?php


namespace Spike\Server;


class Configuration
{
    /**
     * @var string
     */
    protected $address = '0.0.0.0:8090';

    protected $log = [
        'file' => './server.log',
        'level' => 'info'
    ];

    protected $users = [
        [
            'username' => 'admin',
            'password' => 'admin'
        ]
    ];

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