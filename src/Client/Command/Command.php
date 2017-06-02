<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Command;

use Spike\Client;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
        parent::__construct(null);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}