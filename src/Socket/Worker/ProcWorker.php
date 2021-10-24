<?php

namespace Spike\Socket\Worker;

use React\EventLoop\LoopInterface;
use React\Socket\ServerInterface as Socket;
use React\Stream\CompositeStream;
use React\Stream\DuplexResourceStream;
use React\Stream\ReadableResourceStream;
use React\Stream\WritableResourceStream;
use Spike\Command\CommandFactory;
use Spike\Connection\ConnectionInterface;
use Spike\Connection\StreamConnection;
use Spike\Process\Process;
use Spike\Process\ProcProcess;
use Spike\Socket\ServerInterface;
use Spike\Socket\Worker;

class ProcWorker extends Worker
{
    /**
     * @var CommandFactory
     */
    protected $commands;

    /**
     * @var ProcProcess
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

    public function __construct(LoopInterface $loop, ServerInterface $server, Socket $socket)
    {
        parent::__construct($loop, $server, $socket);
        $this->commands = $this->createCommandFactory();
        $this->isSupportSignal = Process::isSupportPosixSignal();
    }

    public function start()
    {
        $config = [
            'address' => $this->server->getOption('address')
        ];
        $entryFile = __DIR__ . '/Internal/worker.php';
        $this->process = new ProcProcess(sprintf("php %s --configuration %s", $entryFile, json_encode($config)));
        $this->process->start(false);
        $this->control = new StreamConnection(new CompositeStream(
            new ReadableResourceStream($this->process->stdout, $this->loop),
            new WritableResourceStream($this->process->stdin, $this->loop),
        ));
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