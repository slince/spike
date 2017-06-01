<?php
/**
 * Spike library
 * @author Tao <taosikai@yeah.net>
 */
namespace Spike;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Server extends Application
{
    const NAME = 'spike-server';

    const VERSION = '1.0.0.dev';

    /**
     * @var Server\Server
     */
    protected $server;

    public function __construct($configFile = null)
    {
        is_null($configFile) || $this->configuration->load($configFile);
        $this->server = new Server\Server($this->configuration->getServerAddress(), null, $this->dispatcher);
        parent::__construct(static::NAME, static::VERSION);
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }
        if (null === $output) {
            $output = new ConsoleOutput();
        }
        return parent::run($input, $output);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        return parent::doRun($input, $output);
    }
}