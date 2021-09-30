<?php


namespace Spike\Socket\Process;

class ProcProcess extends AbstractProcess
{
    /**
     * @inheritDoc
     */
    public function signal($signal)
    {
        
    }

    /**
     * @inheritDoc
     */
    public function onSignal($signal, callable $handler)
    {

    }

    /**
     * @inheritDoc
     */
    public function start($blocking = true)
    {
    }

    /**
     * @inheritDoc
     */
    public function wait()
    {
    }

    /**
     * @inheritDoc
     */
    public function stop()
    {
    }

    /**
     * @inheritDoc
     */
    public function getPid()
    {
    }
}