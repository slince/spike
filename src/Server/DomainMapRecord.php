<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server;

class DomainMapRecord
{
    protected $domain;

    protected $connection;

    public function __construct($domain, $connection)
    {
        $this->domain = $domain;
        $this->connection = $connection;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection;
    }
}