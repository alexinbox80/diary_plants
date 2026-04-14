<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\PestService;
use App\Domain\Service\ModelFactory;
use App\Domain\Model\Pest\CreatePestModel;
use App\Domain\Service\Csv\Import\PestImporter;

class PestImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private PestService $pestService;
    private PestImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->pestService = $this->createMock(PestService::class);

        $this->importer = new PestImporter(
            $this->modelFactory,
            $this->pestService
        );
    }

    public function testSupportsReturnsTrueForPest(): void
    {
        $this->assertTrue($this->importer->supports('pest'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Исходные данные (как будто из CSV)
        $data = [
            'group_id' => '10',
            'marker_id' => '5',
            'title' => 'Актар',
            'amount' => '100',
            'application_rate' => '0.4г/л',
            'manufacturer' => 'Syngenta',
            'description' => '', // пустая строка должна стать null
            'comment' => 'Хорошее средство'
        ];

        // Ожидаемый массив после маппинга (с учетом приведения типов и хелперов)
        $expectedMap = [
            10,
            5,
            'Актар',
            100,
            '0.4г/л',
            'Syngenta',
            null, // описание стало null благодаря es()
            'Хорошее средство'
        ];

        $mockModel = $this->createMock(CreatePestModel::class);

        // 2. Настраиваем ModelFactory: должна вернуть модель, получив $expectedMap
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreatePestModel::class, ...$expectedMap)
            ->willReturn($mockModel);

        // 3. Настраиваем PestService: должен вызвать create с этой моделью
        $this->pestService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        // 4. Запуск
        $this->importer->import($data);
    }
}
