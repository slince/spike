<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Slince\Event\Event;
use Slince\Event\SubscriberInterface;
use Spike\Server\EventStore;
use Spike\Server\Subscriber\ScreenPrettySubscriber;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends Application implements SubscriberInterface
{
    const NAME = 'spike-server';

    const VERSION = '1.0.0.dev';

    /**
     * @var Server\Server
     */
    protected $server;

    public function __construct($configFile = null)
    {
        parent::__construct(static::NAME, static::VERSION);
        is_null($configFile) || $this->configuration->load($configFile);
        $this->server = new Server\Server($this->configuration->getServerAddress(), null, $this->dispatcher);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $commandName = $input->getFirstArgument();
        if ($commandName) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->doRunServer();
        }
        return $exitCode;
    }

    /**
     * Start the server
     */
    protected function doRunServer()
    {
        foreach ($this->getSubscribers() as $subscriber) {
            $this->dispatcher->addSubscriber($subscriber);
        }
        $this->server->run();
    }

    public function getEvents()
    {
        return [];
    }

    /**
     * Gets all subscribers
     * @return array
     */
    public function getSubscribers()
    {
        return [
            $this,
            new ScreenPrettySubscriber($this)
        ];
    }
}