<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\OffspringService;
use App\Domain\Model\Offspring\CreateOffspringModel;
use App\Domain\Service\Csv\Import\OffspringImporter;

class OffspringImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private OffspringService $offspringService;
    private OffspringImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->offspringService = $this->createMock(OffspringService::class);

        $this->importer = new OffspringImporter(
            $this->modelFactory,
            $this->offspringService
        );
    }

    public function testSupportsReturnsTrueForOffspring(): void
    {
        $this->assertTrue($this->importer->supports('offspring'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Данные из CSV (все значения — строки)
        $data = [
            'group_id' => '2',
            'plant_id' => '15',
            'fruiting_date' => '2024-08-10',
            'flowering_date' => '', // Пустая дата должна стать null
            'mass' => '250',
            'color' => 'Red',
            'flavor' => 'Sweet',
            'quantity' => '5',
            'comment' => 'Первый урожай'
        ];

        $mockModel = $this->createMock(CreateOffspringModel::class);

        // 2. Настраиваем проверку вызова ModelFactory
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateOffspringModel::class,
                2,                                      // group_id (int)
                15,                                     // plant_id (int)
                $this->isInstanceOf(\DateTimeImmutable::class), // fruiting_date
                null,                                   // flowering_date (пусто -> null)
                250,                                    // mass (int)
                'Red',                                  // color
                'Sweet',                                // flavor
                5,                                      // quantity (int)
                'Первый урожай'                         // comment
            )
            ->willReturn($mockModel);

        // 3. Проверяем, что созданная модель передана в сервис
        $this->offspringService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesDatesCorrectly(): void
    {
        $data = [
            'group_id' => '1',
            'plant_id' => '1',
            'fruiting_date' => '2024-05-01',
            'flowering_date' => '2024-04-01',
            'mass' => '0',
            'color' => '',
            'flavor' => '',
            'quantity' => '0',
            'comment' => ''
        ];

        // Проверяем конкретные значения дат после трансформации
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->willReturnCallback(function ($class, ...$args) {
                $this->assertEquals('2024-05-01', $args[2]->format('Y-m-d'));
                $this->assertEquals('2024-04-01', $args[3]->format('Y-m-d'));
                return $this->createMock(CreateOffspringModel::class);
            });

        $this->importer->import($data);
    }
}
