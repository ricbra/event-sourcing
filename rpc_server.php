<?php

use Spray\Serializer\Cache\ArrayCache;
use Spray\Serializer\ObjectSerializerBuilder;
use Spray\Serializer\ReflectionRegistry;
use Spray\Serializer\SerializerRegistry;

require 'vendor/autoload.php';

class Command {}

$t = function ($a) {
    return 'abcdef';
};

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

$channelFactory = new \Simgroep\EventSourcing\Messaging\RpcChannelFactory($serializer, $loop, [
    'host' => '192.168.99.100',
    'port' => '32770'
]);

$serverQueue = 'rpc_test';
$channel = $channelFactory->rpcServer($serverQueue);

$consumer = new \Simgroep\EventSourcing\Messaging\RpcConsumer(
    $loop,
    $channelFactory,
    $serializer,
    $serverQueue
);

$consumer->consume($t);

$loop->run();
