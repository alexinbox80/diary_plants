<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\WateringService;
use App\Domain\Model\Watering\CreateWateringModel;
use App\Domain\Service\Csv\Import\WateringImporter;

class WateringImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private WateringService $wateringService;
    private WateringImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->wateringService = $this->createMock(WateringService::class);

        $this->importer = new WateringImporter(
            $this->modelFactory,
            $this->wateringService
        );
    }

    public function testSupportsReturnsTrueForWatering(): void
    {
        $this->assertTrue($this->importer->supports('watering'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Имитация данных из CSV
        $data = [
            'group_id' => '3',
            'marker_id' => '8',
            'amount' => '500',
            'type' => 'Плановый',
            'method' => 'Верхний полив',
            'temperature' => 'Комнатная',
            'description' => 'Отстоянная вода',
            'comment' => 'Добавлен фитоспорин'
        ];

        // 2. Ожидаемые аргументы (числовые значения должны стать int)
        $expectedArgs = [
            3,                  // (int) group_id
            8,                  // (int) marker_id
            500,                // (int) amount
            'Плановый',         // type
            'Верхний полив',    // method
            'Комнатная',        // temperature
            'Отстоянная вода',  // description
            'Добавлен фитоспорин' // comment
        ];

        $mockModel = $this->createMock(CreateWateringModel::class);

        // 3. Проверка: Фабрика получает данные в правильном формате и порядке
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateWateringModel::class, ...$expectedArgs)
            ->willReturn($mockModel);

        // 4. Проверка: Сервис получает созданную модель
        $this->wateringService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesZeroValues(): void
    {
        $data = [
            'group_id' => '1',
            'marker_id' => '0',
            'amount' => '0',
            'type' => 'Опрыскивание',
            'method' => 'Пульверизатор',
            'temperature' => 'Теплая',
            'description' => '',
            'comment' => ''
        ];

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreateWateringModel::class,
                1, 0, 0, 'Опрыскивание', 'Пульверизатор', 'Теплая', '', ''
            )
            ->willReturn($this->createMock(CreateWateringModel::class));

        $this->importer->import($data);
    }
}
