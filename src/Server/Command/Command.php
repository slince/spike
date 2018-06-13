<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike\Server\Command;

use Spike\Server\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

class Command extends BaseCommand
{
    /**
     * @var Application
     */
    protected $server;

    public function __construct(Application $server)
    {
        $this->server = $server;
        parent::__construct(null);
    }

    /**
     * @return Application
     */
    public function getServer()
    {
        return $this->server;
    }
}