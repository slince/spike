<?php
include __DIR__ . '/vendor/autoload.php';

$server = new \Spike\Server\Server('127.0.0.1:80');
$server->run();