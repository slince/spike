<?php
include __DIR__ . '/vendor/autoload.php';

$client = new \Spike\Client\Client('127.0.0.1:80');
$client->addForwardHost('spike.domain.com', 'localhost:8080');
$client->run();