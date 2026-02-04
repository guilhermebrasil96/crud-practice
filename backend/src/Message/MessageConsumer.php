<?php

declare(strict_types=1);

namespace App\Message;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class MessageConsumer
{
    public function __construct(
        private readonly string $dsn,
        private readonly string $queueName,
    ) {
    }

    /** @param callable(string): void $onMessage */
    public function consume(callable $onMessage): void
    {
        $connection = $this->createConnection();
        $channel = $connection->channel();

        $channel->queue_declare($this->queueName, false, true, false, false);
        $channel->basic_qos(null, 1, null);

        $callback = function (AMQPMessage $msg) use ($channel, $onMessage): void {
            $body = method_exists($msg, 'getBody') ? $msg->getBody() : $msg->body;
            $deliveryTag = method_exists($msg, 'getDeliveryTag') ? $msg->getDeliveryTag() : $msg->delivery_info['delivery_tag'];
            $onMessage($body);
            $channel->basic_ack($deliveryTag);
        };

        $channel->basic_consume($this->queueName, '', false, false, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    private function createConnection(): AMQPStreamConnection
    {
        $parsed = parse_url($this->dsn);
        if ($parsed === false || !isset($parsed['host'], $parsed['user'], $parsed['pass'])) {
            throw new \InvalidArgumentException('Invalid RABBITMQ_DSN.');
        }

        $port = (int) ($parsed['port'] ?? 5672);
        $path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
        $vhost = $path === '' ? '/' : rawurldecode($path);

        $connectionTimeout = 10;
        $readWriteTimeout = 60;

        return new AMQPStreamConnection(
            $parsed['host'],
            $port,
            $parsed['user'],
            $parsed['pass'],
            $vhost,
            false,
            'AMQPLAIN',
            null,
            'en_US',
            $connectionTimeout,
            $readWriteTimeout,
        );
    }
}
