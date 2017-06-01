<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Di\Container;
use Slince\Event\Dispatcher;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    const NAME = 'spike-server';

    const VERSION = '1.0.0.dev';
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    public function __construct($name, $version)
    {
        parent::__construct($name, $version);
        $this->dispatcher =  new Dispatcher();
        $this->container = new Container();
        $this->configuration = new Configuration();
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}