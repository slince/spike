<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Client\Command;

use Spike\Client\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Application
     */
    protected $client;

    public function __construct(Application $client)
    {
        $this->client = $client;
        parent::__construct(null);
    }

    /**
     * @return Application
     */
    public function getClient()
    {
        return $this->client;
    }
}