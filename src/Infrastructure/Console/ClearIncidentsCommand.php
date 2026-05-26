<?php

namespace App\Infrastructure\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Application\Message\ClearOldIncidentsMessage;


#[AsCommand(
    name: 'app:incidents:clear',
    description: 'Deletes incidents older than a specified number of days'
)]
class ClearIncidentsCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Просто создаем пустой объект-событие (DTO)
        $message = new ClearOldIncidentsMessage();

        // Отправляем его в шину. Пару миллисекунд — и задача улетела!
        $this->messageBus->dispatch($message);

        $io->success('Задача на очистку инцидентов успешно добавлена в очередь.');

        return Command::SUCCESS;
    }
}
