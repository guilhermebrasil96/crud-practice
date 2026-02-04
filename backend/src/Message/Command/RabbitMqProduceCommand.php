<?php

declare(strict_types=1);

namespace App\Message\Command;

use App\Message\MessagePublisher;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rabbitmq:produce',
    description: 'Publish a test message to the RabbitMQ queue',
)]
final class RabbitMqProduceCommand extends Command
{
    public function __construct(
        private readonly MessagePublisher $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'message',
            InputArgument::OPTIONAL,
            'Message body to send',
            'Hello RabbitMQ! ' . date('Y-m-d H:i:s'),
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');

        try {
            $this->publisher->publish($message);
            $io->success('Message published: ' . $message);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error('Error: ' . $e->getMessage());
            $io->note('Is RabbitMQ running? (docker compose up -d rabbitmq)');
            return Command::FAILURE;
        }
    }
}
