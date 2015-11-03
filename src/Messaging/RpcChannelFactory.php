<?php

namespace Simgroep\EventSourcing\Messaging;

use Bunny\Channel;
use Bunny\Async\Client;
use React\Promise\PromiseInterface;
use React\EventLoop\LoopInterface;
use Simgroep\EventSourcing\Messaging\Exception\RpcMessageException;
use Spray\Serializer\SerializerInterface;

class RpcChannelFactory
{
    /**
     * @var PromiseInterface
     */
    private $connection;

    /**
     * @var LoopInterface
     */
    private $loop;

    private $options = [];

    private $serverQueues = [];

    private $callbackQueue;

    public function __construct(
        SerializerInterface $serializer,
        LoopInterface $loop,
        array $options
    ) {
        $this->serializer = $serializer;
        $this->loop = $loop;
        $this->options = $options;
    }

    /**
     * @return PromiseInterface
     */
    public function connect()
    {
        if (null === $this->connection) {
            $client = new Client($this->loop, $this->options);
            $this->connection = $client->connect()
                ->then(
                    function (Client $client) {
                        return $client->channel();
                    },
                    function (\Exception $exception) {
                        $this->loop->stop();
                        throw new RpcMessageException(
                            sprintf(
                                'Could not connect to rabbitmq: %s on line %s in file %s',
                                $exception->getMessage(),
                                $exception->getLine(),
                                $exception->getFile()
                            ),
                            0,
                            $exception
                        );
                    }
                );
        }
        return $this->connection;
    }

    public function rpc($serverQueueName, $callbackQueue)
    {
        if (! isset($this->serverQueues[$serverQueueName])) {
            $this->serverQueues[$serverQueueName] = $this->connect()->then(
                function (Channel $channel) use ($serverQueueName, $callbackQueue) {
                    return \React\Promise\all([
                        $channel,
                        $channel->queueDeclare($serverQueueName, false, true),
                        $channel->queueDeclare($callbackQueue, false, false, true)
                    ]);
                },
                function (\Exception $exception) {
                    $this->loop->stop();
                    throw new RpcMessageException(
                        sprintf(
                            'Could not create channel and exchange: %s on line %s in file %s',
                            $exception->getMessage(),
                            $exception->getLine(),
                            $exception->getFile()
                        ),
                        0,
                        $exception
                    );
                }
            );
        }

        return $this->serverQueues[$serverQueueName];
    }

    public function rpcServer($serverQueueName)
    {
        if (! isset($this->serverQueues[$serverQueueName])) {
            $this->serverQueues[$serverQueueName] = $this->connect()->then(
                function (Channel $channel) use ($serverQueueName) {
                    return \React\Promise\all([
                        $channel,
                        $channel->queueDeclare($serverQueueName, false, true),
                    ]);
                },
                function (\Exception $exception) {
                    $this->loop->stop();
                    throw new RpcMessageException(
                        sprintf(
                            'Could not create channel and exchange: %s on line %s in file %s',
                            $exception->getMessage(),
                            $exception->getLine(),
                            $exception->getFile()
                        ),
                        0,
                        $exception
                    );
                }
            );
        }

        return $this->serverQueues[$serverQueueName];
    }
}
