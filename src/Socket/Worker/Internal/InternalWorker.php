<?php

namespace Spike\Socket\Worker\Internal;

final class InternalWorker
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function run()
    {

    }
}