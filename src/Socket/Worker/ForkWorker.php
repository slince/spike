<?php

namespace Spike\Socket\Worker;

use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface as Socket;
use React\Stream\CompositeStream;
use React\Stream\DuplexResourceStream;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Spike\Command\CommandFactory;
use Spike\Command\CommandInterface;
use Spike\Connection\ConnectionInterface;
use Spike\Connection\StreamConnection;
use Spike\Exception\RuntimeException;
use Spike\Process\Process;
use Spike\Protocol\Message;
use Spike\Protocol\MessageParser;
use Spike\Socket\ServerInterface;
use Spike\Socket\Worker;

class ForkWorker extends Worker
{
    /**
     * @var CommandFactory
     */
    protected $commands;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var ConnectionInterface
     */
    protected $control;

    /**
     * @var bool
     */
    protected $isSupportSignal = false;

    protected $inChildProcess = false;

    public function __construct(LoopInterface $loop, ServerInterface $server)
    {
        parent::__construct($loop, $server);
        $this->commands = $this->createCommandFactory();
        $this->isSupportSignal = Process::isSupportPosixSignal();
    }

    public function start()
    {
        $this->process = new Process($this->createCallable());
        if ($this->isSupportSignal) {
            $this->registerSignals();
        }
        $this->process->start(false);
        $this->control = new StreamConnection(new CompositeStream(
            new ReadableResourceStream($this->process->stdout, $this->loop),
            new WritableResourceStream($this->process->stdin, $this->loop),
        ));
    }

    public function close(bool $grace = false)
    {
        // 如果支持信号，优先使用信号
        if ($this->isSupportSignal) {
            $this->process->signal($grace ? SIGHUP : SIGTERM);
        } else {
            $this->control->executeCommand(new Command\CLOSE($grace));
        }
        parent::close();
    }

    protected function registerSignals()
    {
        foreach ($this->signals as $signal => $handler) {
            $this->process->registerSignal($signal, $handler);
        }
        $this->process->registerSignal([SIGINT, SIGTERM], function(){
            $this->handleClose(false);
        });
        $this->process->registerSignal([SIGHUP], function(){
            $this->handleClose(true);
        });
    }

    public function createCallable(): \Closure
    {
        return function($stdin, $stdout, $stderr){
            $this->inChildProcess = true;

            $connection = new StreamConnection(new CompositeStream(
                new ReadableResourceStream($stdin, $this->loop),
                new WritableResourceStream($stdout, $this->loop)
            ));

            $this->listenCommands($connection);

            $this->work();

            $this->loop->run();
        };
    }

    protected function listenCommands(ConnectionInterface $connection)
    {
        $connection->on('message', function(Message $message, ConnectionInterface $connection){
            $command = $this->commands->createCommand($message);
            $this->handleCommand($command, $connection);
        });

        $parser = new MessageParser($connection);
        $parser->parse();
    }

    protected function handleCommand(CommandInterface $command, ConnectionInterface $connection)
    {
        switch ($command->getCommandId()) {
            case 'CLOSE':
                $this->handleClose($command->isGrace());
                break;
        }
    }

    /**
     * {@internal}
     */
    public function handleClose(bool $grace)
    {
        if (!$this->inChildProcess) {
            throw new RuntimeException('The action can only be executed in child process.');
        }
        if ($grace) {
            $this->loop->stop();
        }
        exit(0);
    }

    /**
     * Create command factory for the server.
     *
     * @return CommandFactory
     */
    protected function createCommandFactory(): CommandFactory
    {
        return new CommandFactory([
            'CLOSE' => Command\CLOSE::class,
        ]);
    }
}