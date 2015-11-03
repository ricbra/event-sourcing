<?php

use Spray\Serializer\Cache\ArrayCache;
use Spray\Serializer\ObjectSerializerBuilder;
use Spray\Serializer\ReflectionRegistry;
use Spray\Serializer\SerializerRegistry;

require 'vendor/autoload.php';

class Command {}

$loop = React\EventLoop\Factory::create();

$serializer = new \Spray\Serializer\Serializer(
    new \Spray\Serializer\SerializerLocator(
        new SerializerRegistry(),
        new ObjectSerializerBuilder(
            new ReflectionRegistry()
        ),
        new ArrayCache()
    )
);

$options = [
    'host' => '192.168.99.100',
    'port' => '32770'
];

$serverQueue = 'rpc_test';

$publisher = new \Simgroep\EventSourcing\Messaging\RpcPublisher(
    $loop,
    $serializer,
    $serverQueue,
    $options
);
$publisher->publish(new Command());

$loop->run();
