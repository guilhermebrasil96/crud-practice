<?php

declare(strict_types=1);

namespace App\Message\Command;

use App\Message\MessageConsumer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rabbitmq:consume',
    description: 'Consume messages from the RabbitMQ queue (Ctrl+C to exit)',
)]
final class RabbitMqConsumeCommand extends Command
{
    public function __construct(
        private readonly MessageConsumer $consumer,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('RabbitMQ consumer');
        $io->note('Press Ctrl+C to stop.');

        try {
            $this->consumer->consume(function (string $body) use ($output): void {
                $output->writeln('[<info>' . date('H:i:s') . '</info>] ' . $body);
            });
        } catch (\Throwable $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
