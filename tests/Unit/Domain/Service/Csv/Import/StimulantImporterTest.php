<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\StimulantService;
use App\Domain\Model\Stimulant\CreateStimulantModel;
use App\Domain\Service\Csv\Import\StimulantImporter;

class StimulantImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private StimulantService $stimulantService;
    private StimulantImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->stimulantService = $this->createMock(StimulantService::class);

        $this->importer = new StimulantImporter(
            $this->modelFactory,
            $this->stimulantService
        );
    }

    public function testSupportsReturnsTrueForStimulant(): void
    {
        $this->assertTrue($this->importer->supports('stimulant'));
        $this->assertFalse($this->importer->supports('plant'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // 1. Данные из CSV
        $data = [
            'group_id' => '7',
            'marker_id' => '12',
            'title' => 'Эпин-Экстра',
            'amount' => '50',
            'application_rate' => '1 капля на 100мл',
            'manufacturer' => 'НЭСТ М',
            'description' => 'Адаптоген',
            'comment' => '' // Должно стать null через es()
        ];

        // 2. Ожидаемые аргументы после маппинга
        $expectedArgs = [
            7,                          // (int) group_id
            12,                         // (int) marker_id
            'Эпин-Экстра',              // title
            50,                         // (int) amount
            '1 капля на 100мл',         // application_rate
            'НЭСТ М',                   // manufacturer
            'Адаптоген',                // description
            null                        // es(comment)
        ];

        $mockModel = $this->createMock(CreateStimulantModel::class);

        // 3. Проверка вызова фабрики
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateStimulantModel::class, ...$expectedArgs)
            ->willReturn($mockModel);

        // 4. Проверка передачи в сервис
        $this->stimulantService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }
}
