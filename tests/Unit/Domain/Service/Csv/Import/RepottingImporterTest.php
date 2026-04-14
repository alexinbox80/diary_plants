<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\RepottingService;
use App\Domain\Model\Repotting\CreateRepottingModel;
use App\Domain\Service\Csv\Import\RepottingImporter;

class RepottingImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private RepottingService $repottingService;
    private RepottingImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->repottingService = $this->createMock(RepottingService::class);

        $this->importer = new RepottingImporter(
            $this->modelFactory,
            $this->repottingService
        );
    }

    public function testSupportsReturnsTrueForRepotting(): void
    {
        $this->assertTrue($this->importer->supports('repotting'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Данные из CSV
        $data = [
            'group_id' => '4',
            'plant_id' => '120',
            'repotted_at' => '2024-05-20',
            'type' => 'Перевалка',
            'pot_material' => 'Керамика',
            'pot_size' => '15см',
            'comment' => 'Добавлен дренаж'
        ];

        $mockModel = $this->createMock(CreateRepottingModel::class);

        // 2. Проверка трансформации данных в методе map()
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateRepottingModel::class,
                4,                                      // group_id (int)
                120,                                    // plant_id (int)
                $this->isInstanceOf(\DateTimeImmutable::class), // repotted_at (DateTime)
                'Перевалка',                            // type
                'Керамика',                             // pot_material
                '15см',                                 // pot_size
                'Добавлен дренаж'                       // comment
            )
            ->willReturn($mockModel);

        // 3. Проверка вызова сервиса
        $this->repottingService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesEmptyDate(): void
    {
        $data = [
            'group_id' => '1',
            'plant_id' => '1',
            'repotted_at' => '', // Пустая строка
            'type' => 'Плановая',
            'pot_material' => 'Пластик',
            'pot_size' => '10',
            'comment' => ''
        ];

        // Проверяем, что пустая дата стала null
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateRepottingModel::class,
                1, 1, null, 'Плановая', 'Пластик', '10', ''
            )
            ->willReturn($this->createMock(CreateRepottingModel::class));

        $this->importer->import($data);
    }
}
