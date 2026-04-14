<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\UsageService;
use App\Domain\Model\Usage\CreateUsageModel;
use App\Domain\Service\Csv\Import\UsageImporter;

class UsageImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private UsageService $usageService;
    private UsageImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->usageService = $this->createMock(UsageService::class);

        $this->importer = new UsageImporter(
            $this->modelFactory,
            $this->usageService
        );
    }

    public function testSupportsReturnsTrueForUsage(): void
    {
        $this->assertTrue($this->importer->supports('usage'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Данные из CSV (обработка препарата для конкретного растения)
        $data = [
            'group_id' => '5',
            'plant_id' => '102',
            'use_date' => '2024-04-12',
            'usable_id' => '16',     // ID препарата (pest/fertilizer)
            'usable_type' => 'pest', // Тип препарата
            'comment' => ''          // Должно стать null
        ];

        $mockModel = $this->createMock(CreateUsageModel::class);

        // 2. Проверка маппинга: IDs -> int, дата -> DateTime, пустая строка -> null
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateUsageModel::class,
                5,                                      // group_id
                102,                                    // plant_id
                $this->isInstanceOf(\DateTimeImmutable::class), // use_date
                16,                                     // usable_id
                'pest',                                 // usable_type
                null                                    // comment (через es())
            )
            ->willReturn($mockModel);

        // 3. Проверка передачи в сервис
        $this->usageService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesFullData(): void
    {
        $data = [
            'group_id' => '1',
            'plant_id' => '1',
            'use_date' => '2024-04-13',
            'usable_id' => '1',
            'usable_type' => 'fertilizer',
            'comment' => 'Плановая подкормка'
        ];

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateUsageModel::class,
                1, 1, $this->anything(), 1, 'fertilizer', 'Плановая подкормка'
            )
            ->willReturn($this->createMock(CreateUsageModel::class));

        $this->importer->import($data);
    }
}
