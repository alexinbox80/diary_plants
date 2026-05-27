<?php

namespace App\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\Message\ClearOldIncidentsMessage;

#[AsCommand(
    name: 'app:incidents:clear',
    description: 'Deletes incidents older than a specified number of days'
)]
final class ClearIncidentsCommand extends Command
{
    public function __construct(
        private readonly int $incidentsDaysToKeep,
        private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'days',
            InputArgument::OPTIONAL, // Сделать необязательным
            'Количество дней, за которые нужно хранить инциденты',
            $this->incidentsDaysToKeep // Значение по умолчанию, если пользователь ничего не ввел
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Например, если добавите аргумент: php bin/console app:incidents:clear 60
        $days = $input->getArgument('days') ?? $this->incidentsDaysToKeep;

        // Просто создаем пустой объект-событие (DTO)
        $message = new ClearOldIncidentsMessage($days);

        // Отправляем его в шину. Пару миллисекунд — и задача улетела!
        $this->messageBus->dispatch($message);

        $io->success('Задача на очистку инцидентов успешно добавлена в очередь.');

        return Command::SUCCESS;
    }
}
