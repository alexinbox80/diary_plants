<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\MarkerService;
use App\Domain\Model\Marker\CreateMarkerModel;
use App\Domain\Service\Csv\Import\MarkerImporter;

class MarkerImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private MarkerService $markerService;
    private MarkerImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->markerService = $this->createMock(MarkerService::class);

        $this->importer = new MarkerImporter(
            $this->modelFactory,
            $this->markerService
        );
    }

    public function testSupportsReturnsTrueForMarker(): void
    {
        $this->assertTrue($this->importer->supports('marker'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Имитация данных из CSV
        $data = [
            'group_id' => '5',
            'letter' => 'A',
            'color' => '#FF0000',
            'type' => 'status',
            'description' => 'Активное состояние',
            'color_description' => 'Красный маркер'
        ];

        // 2. Ожидаемые аргументы (group_id должен стать int)
        $expectedArgs = [
            5,
            'A',
            '#FF0000',
            'status',
            'Активное состояние',
            'Красный маркер'
        ];

        $mockModel = $this->createMock(CreateMarkerModel::class);

        // 3. Проверка: Factory получает массив в нужном порядке
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateMarkerModel::class, ...$expectedArgs)
            ->willReturn($mockModel);

        // 4. Проверка: Сервис получает созданную модель
        $this->markerService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }
}
