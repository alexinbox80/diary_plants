<?php

namespace Unit\Infrastructure\Console;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\Envelope;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Application\Message\ClearOldIncidentsMessage;
use App\Infrastructure\Console\ClearIncidentsCommand;

#[CoversClass(ClearIncidentsCommand::class)]
final class ClearIncidentsCommandTest extends TestCase
{
    private MessageBusInterface $messageBus;
    private int $defaultDaysToKeep;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->defaultDaysToKeep = 30;
    }

    #[Test]
    public function testExecuteDispatchesMessageWithDefaultDays(): void
    {
        // 1. Ожидаем, что в шину уйдет сообщение со значением по умолчанию (30)
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (ClearOldIncidentsMessage $message) {
                return $message->daysToKeep === 30;
            }))
            ->willReturn(new Envelope(new ClearOldIncidentsMessage(30)));

        // 2. Инициализируем команду и тестер
        $command = new ClearIncidentsCommand($this->defaultDaysToKeep, $this->messageBus);
        $commandTester = new CommandTester($command);

        // 3. Запускаем команду без передачи аргументов
        $statusCode = $commandTester->execute([]);

        // 4. Проверки
        $output = $commandTester->getDisplay();
        $this->assertSame(Command::SUCCESS, $statusCode);
        $this->assertStringContainsString('Задача на очистку инцидентов успешно добавлена в очередь.', $output);
    }

    #[Test]
    public function testExecuteDispatchesMessageWithCustomDaysFromArgument(): void
    {
        // 1. Ожидаем, что в шину уйдет сообщение со значением из консоли (15)
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (ClearOldIncidentsMessage $message) {
                return $message->daysToKeep === 15;
            }))
            ->willReturn(new Envelope(new ClearOldIncidentsMessage(15)));

        // 2. Инициализируем команду
        $command = new ClearIncidentsCommand($this->defaultDaysToKeep, $this->messageBus);
        $commandTester = new CommandTester($command);

        // 3. Запускаем команду с явным указанием аргумента 'days'
        $statusCode = $commandTester->execute([
            'days' => 15
        ]);

        // 4. Проверки
        $this->assertSame(Command::SUCCESS, $statusCode);
    }
}
