<?php


namespace Spike\Process;

abstract class AbstractProcess implements ProcessInterface
{
    /**
     * Signal Handler
     *
     * @var array
     */
    protected $signalHandlers = [];

    /**
     * {@inheritdoc}
     */
    public function onSignal($signal, callable $handler)
    {
        $this->signalHandlers[$signal] = $handler;
    }
}