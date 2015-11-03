<?php

namespace Simgroep\EventSourcing\Messaging\Exception;

class RpcMessageException extends \RuntimeException implements Exception
{
    public static function connectionFailed(\Exception $previous = null)
    {
        return new static(
            'Failed opening a connection to RabbitMQ',
            0,
            $previous
        );
    }

    public static function declareServerQueueFailed($serverQueueName, \Exception $previous = null)
    {
        return new static(
            sprintf(
                'Declaring server queue with name "%s" failed',
                $serverQueueName
            ),
            0,
            $previous
        );
    }

    public static function declareCallbackQueueFailed(\Exception $previous = null)
    {
        return new static(
            sprintf(
                'Declaring callback queue failed'
            ),
            0,$previous
        );
    }

    public static function publishServerQueueFailed($serverQueueName, \Exception $previous = null)
    {
        return new static(
            sprintf(
                'Publishing message on server queue with name "%s" failed',
                $serverQueueName
            ),
            0,
            $previous
        );
    }

    public static function missingCorrelationId(\Exception $previous = null)
    {
        return new static(
            'Server returned response without correlation-id',
            0,
            $previous
        );
    }

    public static function unknownCorrelationId($correlationId, \Exception $previous = null)
    {
        return new static(
            sprintf(
                'Server responded with unknown correlation-id: "%s"',
                $correlationId
            ),
            0,
            $previous
        );
    }

    public static function consumeFailed(\Exception $previous = null)
    {
        return new static(
            'Could not consume message',
            0,
            $previous
        );
    }
}
