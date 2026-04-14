<?php

namespace Unit\Domain\Service\Csv;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\Csv\CsvService;
use App\Domain\Service\Csv\Import\EntityImporterInterface;

class CsvServiceTest extends TestCase
{
    private string $tempFile;

    protected function setUp(): void
    {
        // Создаем путь для файла с точкой в названии
        $this->tempFile = sys_get_temp_dir() . '/01.plant.csv';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    public function testProcessSuccessfulImport(): void
    {
        // 1. Готовим данные CSV
        file_put_contents($this->tempFile, "id;title\n1;Rose\n2;Cactus");

        // 2. Создаем мок импортера
        $importer = $this->createMock(EntityImporterInterface::class);

        $importer->method('supports')
            ->with('plant')
            ->willReturn(true);

        // 3. Проверяем аргументы через Callback (замена withConsecutive)
        $matcher = $this->exactly(2);
        $importer->expects($matcher)
            ->method('import')
            ->willReturnCallback(function (array $data) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEquals(['id' => '1', 'title' => 'Rose'], $data),
                    2 => $this->assertEquals(['id' => '2', 'title' => 'Cactus'], $data),
                    default => null
                };
            });

        // 4. Запуск сервиса
        $service = new CsvService(';', [$importer]);
        $result = $service->process($this->tempFile);

        $this->assertEquals(2, $result);
    }

    public function testProcessThrowsExceptionIfNoImporterFound(): void
    {
        file_put_contents($this->tempFile, "id;title\n1;Test");

        $service = new CsvService(';', []);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No importer found for plant");

        $service->process($this->tempFile);
    }
}
