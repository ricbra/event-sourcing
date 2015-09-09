<?php

namespace Simgroep\EventSourcing\EventHandling;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventListenerInterface;
use Simgroep\EventSourcing\Messaging\DomainEventStreamMessage;
use Simgroep\EventSourcing\Messaging\Queue;

class PublishMessageToQueue implements EventListenerInterface
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * @param Queue $queue
     */
    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * @param DomainMessage $domainMessage
     */
    public function handle(DomainMessage $domainMessage)
    {
        $this->queue->publish(new DomainEventStreamMessage(
            new DomainEventStream(array($domainMessage->getPayload()))
        ));
    }
}
