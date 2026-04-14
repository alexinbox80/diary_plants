<?php

namespace Unit\Controller\Cli;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\Csv\CsvService;
use App\Controller\Cli\ConvertCSVCommand;
use Symfony\Component\Console\Tester\CommandTester;

class ConvertCSVCommandTest extends TestCase
{
    private CsvService $csvService;
    private string $fakeCsvDir;

    protected function setUp(): void
    {
        $this->csvService = $this->createMock(CsvService::class);

        // Создаем временную директорию для эмуляции папки с CSV
        $this->fakeCsvDir = sys_get_temp_dir() . '/csv_test_' . uniqid() . '/';
        mkdir($this->fakeCsvDir);
    }

    protected function tearDown(): void
    {
        // Удаляем временные файлы и папку
        if (is_dir($this->fakeCsvDir)) {
            $files = array_diff(scandir($this->fakeCsvDir), ['.', '..']);
            foreach ($files as $file) {
                unlink($this->fakeCsvDir . $file);
            }
            rmdir($this->fakeCsvDir);
        }
    }

    public function testExecuteFailsIfDirectoryDoesNotExist(): void
    {
        // Указываем заведомо несуществующий путь
        $invalidPath = '/non/existent/path/to/csv/';

        $command = new ConvertCSVCommand($invalidPath, $this->csvService);
        $commandTester = new CommandTester($command);

        // Сервис не должен вызываться вообще
        $this->csvService->expects($this->never())->method('process');

        // Если вы добавили проверку is_dir (пункт 1), тест пройдет успешно.
        // Если не добавляли, PHPUnit перехватит Warning от scandir как ошибку.
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();

        // Проверяем наличие сообщения об ошибке (если добавили проверку в код)
        $this->assertStringContainsString('Директория не найдена', $output);

        // Если проверка в коде есть, код возврата будет Command::FAILURE (1)
        $this->assertEquals(1, $commandTester->getStatusCode());
    }

    public function testExecuteProcessesAllFiles(): void
    {
        // 1. Создаем два фиктивных файла в папке
        file_put_contents($this->fakeCsvDir . '01.plant.csv', 'data');
        file_put_contents($this->fakeCsvDir . '02.group.csv', 'data');

        // 2. Настраиваем ожидание: сервис должен быть вызван для каждого файла
        $this->csvService->expects($this->exactly(2))
            ->method('process')
            ->willReturnCallback(function ($path) {
                if (str_contains($path, '01.plant.csv')) return 10;
                if (str_contains($path, '02.group.csv')) return 5;
                return 0;
            });

        // 3. Создаем команду и тестер
        $command = new ConvertCSVCommand($this->fakeCsvDir, $this->csvService);
        $commandTester = new CommandTester($command);

        // 4. Запускаем
        $commandTester->execute([]);

        // 5. Проверяем вывод в консоли
        $output = $commandTester->getDisplay();

        $this->assertStringContainsString('Run command database:convert:csv', $output);
        $this->assertStringContainsString('From file 01.plant.csv 10 records were created', $output);
        $this->assertStringContainsString('From file 02.group.csv 5 records were created', $output);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithEmptyDirectory(): void
    {
        $command = new ConvertCSVCommand($this->fakeCsvDir, $this->csvService);
        $commandTester = new CommandTester($command);

        // В папке только . и .. которые scandir отфильтрует
        $this->csvService->expects($this->never())->method('process');

        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}

