<?php

namespace Simgroep\EventSourcing\Messaging;

use Bunny\Async\Client;
use Bunny\Channel;
use Bunny\Message;
use Exception;
use React\EventLoop\LoopInterface;
use Simgroep\EventSourcing\Messaging\Exception\RpcMessageException;
use Spray\Serializer\SerializerInterface;
use React\Promise\PromiseInterface;

class RpcPublisher implements Publisher
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $serverQueue;

    /**
     * @var string
     */
    private $callbackQueue;

    /**
     * @var array
     */
    private $correlationIds = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var PromiseInterface
     */
    private $connection;

    /**
     * @var PromiseInterface
     */
    private $client;

    /**
     * @var \Bunny\Channel $channel
     */
    private $channel;

    public function __construct(
        LoopInterface $loop,
        SerializerInterface $serializer,
        $serverQueue,
        array $options
    ) {
        $this->loop = $loop;
        $this->serializer = $serializer;
        $this->serverQueue = $serverQueue;
        $this->options = $options;
    }

    /**
     * @param mixed $payload
     *
     * @return void
     */
    public function publish($payload)
    {
        $message = new CommandMessage($payload);

        $this->publishMessage($message);
    }

    /**
     * @return PromiseInterface
     */
    public function connect()
    {
        if (null === $this->connection) {
            $this->client = new Client($this->loop, $this->options);
            $this->connection = $this->client->connect()
                ->then(
                    function (Client $client) {
                        return $client->channel();
                    },
                    function (Exception $exception) {
                        $this->loop->stop();
                        throw RpcMessageException::connectionFailed($exception);
                    }
                );
        }
        return $this->connection;
    }

    public function init()
    {
        $serverQueue = $this->serverQueue;

        return $this->connect()->then(
            function (Channel $channel) use ($serverQueue) {
                return \React\Promise\all([
                    $channel,
                    $channel->queueDeclare($serverQueue, false, true),
                ]);
            }
        )->then(
            function ($v) {
                /** @var \Bunny\Channel $channel */
                $channel = $v[0];
                $this->channel = $channel;

                return $channel->queueDeclare("", false, false, true);
            },
            function (\Exception $e) {
                $this->loop->stop();

                throw RpcMessageException::declareCallbackQueueFailed($e);
            }
        )->then(function ($callbackQueue) {
            $this->callbackQueue = $callbackQueue->queue;

            return $this->channel;
        });
    }

    /**
     * @param QueueMessage $message
     * @return mixed
     */
    protected function publishMessage(QueueMessage $message)
    {
        return $this->init()->then(
            function (Channel $channel) use ($message) {
                $correlationId = uniqid("", true);
                $channel->publish(
                    json_encode(
                        $this->serializer->serialize(
                            $message
                        )
                    ),
                    [
                        "correlation-id" => $correlationId,
                        "reply-to" => $this->callbackQueue,
                    ],
                    "",
                    $this->serverQueue
                );

                $this->correlationIds[] = $correlationId;

                return true;
            }
        )->then(
            function () {
                return $this->channel->consume(function (Message $message) {
                    $correlationId = $message->getHeader("correlation-id");

                    if (! isset($correlationId)) {
                        throw RpcMessageException::missingCorrelationId();
                    }
                    if (! in_array($correlationId, $this->correlationIds)) {
                        throw RpcMessageException::unknownCorrelationId($correlationId);
                    }
                    $this->channel->ack($message);
                    $this->loop->stop();

                }, $this->callbackQueue);
            },
            function (\Exception $e) {
                $this->loop->stop();

                throw RpcMessageException::publishServerQueueFailed($this->serverQueue, $e);
            }
        );
    }
}
