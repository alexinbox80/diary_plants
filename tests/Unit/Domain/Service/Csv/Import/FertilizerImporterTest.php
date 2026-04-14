<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\FertilizerService;
use App\Domain\Service\Csv\Import\FertilizerImporter;
use App\Domain\Model\Fertilizer\CreateFertilizerModel;

class FertilizerImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private FertilizerService $fertilizerService;
    private FertilizerImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->fertilizerService = $this->createMock(FertilizerService::class);

        $this->importer = new FertilizerImporter(
            $this->modelFactory,
            $this->fertilizerService
        );
    }

    public function testSupportsReturnsTrueForFertilizer(): void
    {
        $this->assertTrue($this->importer->supports('fertilizer'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Входные данные из CSV (строковые значения)
        $data = [
            'group_id' => '1',
            'marker_id' => '42',
            'title' => 'Осмокот',
            'amount' => '500',
            'application_rate' => '3г на литр',
            'manufacturer' => 'Everris',
            'description' => '', // Должно стать null
            'comment' => 'Длительное действие'
        ];

        // 2. Ожидаемый маппинг после обработки хелперами
        $expectedArgs = [
            1,                      // (int) group_id
            42,                     // (int) marker_id
            'Осмокот',              // title
            500,                    // (int) amount
            '3г на литр',           // application_rate
            'Everris',              // manufacturer
            null,                   // es(description)
            'Длительное действие'   // es(comment)
        ];

        $mockModel = $this->createMock(CreateFertilizerModel::class);

        // 3. Проверка: Factory должна получить правильный порядок аргументов
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateFertilizerModel::class, ...$expectedArgs)
            ->willReturn($mockModel);

        // 4. Проверка: Сервис должен получить созданную модель
        $this->fertilizerService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }
}
