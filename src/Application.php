<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Slince\Di\Container;
use Slince\Event\Dispatcher;
use Spike\Logger\Logger;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
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

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var LoopInterface
     */
    protected $loop;


    public function __construct(Configuration $configuration, $name = null, $version = null)
    {
        $this->configuration = $configuration;
        $this->container = new Container();
        $this->dispatcher =  new Dispatcher();
        $this->loop = Factory::create();
        parent::__construct($name, $version);
    }

    /**
     * Gets the dispatcher
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

    /**
     * Gets the loop instance
     * @return LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * Gets the logger instance
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }
}