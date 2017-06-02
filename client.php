<?php
include __DIR__ . '/vendor/autoload.php';

$client = new \Spike\Client\Client('127.0.0.1:80');
$client->addForwardHost('spike.domain.com', 'www.baidu.com');
$client->addForwardHost('127.0.0.1', 'www.baidu.com');
$client->run();