<?php

use Spike\Socket\Worker\Internal\InternalWorker;

$config = json_decode($argv[1]) ?: [];
print_r($config);

$worker = new InternalWorker($config);
$worker->run();
