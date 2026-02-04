<?php

declare(strict_types=1);

namespace App\Message;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

final class MessagePublisher
{
    public function __construct(
        private readonly string $dsn,
        private readonly string $queueName,
    ) {
    }

    public function publish(string $body): void
    {
        $connection = $this->createConnection();
        $channel = $connection->channel();

        try {
            $channel->queue_declare($this->queueName, false, true, false, false);

            $message = new AMQPMessage($body, [
                'delivery_mode' => 2,
                'content_type' => 'text/plain',
            ]);

            $channel->basic_publish($message, '', $this->queueName);
        } finally {
            $channel->close();
            $connection->close();
        }
    }

    private function createConnection(): AMQPStreamConnection
    {
        $parsed = parse_url($this->dsn);
        if ($parsed === false || !isset($parsed['host'], $parsed['user'], $parsed['pass'])) {
            throw new \InvalidArgumentException('Invalid RABBITMQ_DSN. Set RABBITMQ_DSN in .env (e.g. amqp://user:password@host:5672/%2F).');
        }

        $port = (int) ($parsed['port'] ?? 5672);
        $path = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
        $vhost = $path === '' ? '/' : rawurldecode($path);

        return new AMQPStreamConnection(
            $parsed['host'],
            $port,
            $parsed['user'],
            $parsed['pass'],
            $vhost,
        );
    }
}
