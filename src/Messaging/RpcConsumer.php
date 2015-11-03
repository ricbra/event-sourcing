<?php

namespace Simgroep\EventSourcing\Messaging;

use Bunny\Channel;
use Bunny\Message;
use Exception;
use React\EventLoop\LoopInterface;
use Simgroep\EventSourcing\Messaging\Exception\RpcMessageException;
use Spray\Serializer\SerializerInterface;

class RpcConsumer implements Consumer
{
    /**
     * @var LoopInterface
     */
    private $loop;

    /**
     * @var ChannelFactory
     */
    private $channelFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param LoopInterface $loop
     * @param RpcChannelFactory $channelFactory
     * @param SerializerInterface $serializer
     * @param string $serverQueueName
     */
    public function __construct(
        LoopInterface $loop,
        RpcChannelFactory $channelFactory,
        SerializerInterface $serializer,
        $serverQueueName
    ) {
        $this->loop = $loop;
        $this->channelFactory = $channelFactory;
        $this->serializer = $serializer;
        $this->serverQueueName = (string) $serverQueueName;
    }

    /**
     * @param callable $callback
     *
     * @return void
     */
    public function consume(callable $callback)
    {
        $this->consumeMessage(function (QueueMessage $message) use ($callback) {
            $callback($message->getPayload());
        });
    }

    /**
     * @param callable $callback
     *
     * @return void
     */
    protected function consumeMessage(callable $callback)
    {
        $this->channelFactory->rpcServer($this->serverQueueName)
            ->then(
                function ($r) use ($callback) {
                    /** @var Channel $channel */
                    $channel = $r[0];
                    return $channel->consume(function (Message $message) use ($callback, $channel) {
                        $data = json_decode($message->content, true);
                        $callback($this->serializer->deserialize(null, $data));
                        $channel->publish(json_encode(['result' => 'ok']), [
                            "correlation-id" => $message->getHeader("correlation-id"),
                        ], "", $message->getHeader("reply-to"));

                        $channel->ack($message);
                    }, $this->serverQueueName);
                },
                function (Exception $exception) {
                    $this->loop->stop();
                    throw RpcMessageException::consumeFailed($exception);
                }
            );
    }
}
