<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use App\Domain\Model\Plant\CreatePlantModel;
use App\Domain\Service\Csv\Import\PlantImporter;

class PlantImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private PlantService $plantService;
    private PlantImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->plantService = $this->createMock(PlantService::class);

        $this->importer = new PlantImporter(
            $this->modelFactory,
            $this->plantService
        );
    }

    public function testSupportsReturnsTrueForPlant(): void
    {
        $this->assertTrue($this->importer->supports('plant'));
        $this->assertFalse($this->importer->supports('other'));
    }

    public function testImportSuccessfullyMapsAllFields(): void
    {
        // 1. Имитируем полную строку из CSV
        $data = [
            'group_id' => '3',
            'title' => 'Лимон Мейера',
            'room' => 'Гостиная',
            'is_shown' => '1',
            'description' => 'Ароматный цитрус',
            'purchase_date' => '2024-01-15',
            'vaccination_date' => '', // Пустая дата -> null
            'planting_date' => '2024-02-10',
            'seller' => 'ЦитрусМикс',
            'nursery' => 'Италия',
            'price' => '1500 RUR',
            'shipping_cost' => '300 RUR',
            'packaging_cost' => '', // Пустая цена -> null
            'soil' => 'Для цитрусовых',
            'is_sold' => 'false',
            'selling_date' => '',
            'selling_price' => '',
            'comment' => 'Требует много света'
        ];

        $mockModel = $this->createMock(CreatePlantModel::class);

        // 2. Проверка: ожидаем вызов фабрики со всеми трансформированными данными
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(
                CreatePlantModel::class,
                3,                                   // (int) group_id
                'Лимон Мейера',                      // title
                'Гостиная',                         // room
                true,                                // str2bool('1')
                'Ароматный цитрус',                  // description
                $this->isInstanceOf(\DateTimeImmutable::class), // purchase_date
                null,                                // vaccination_date (пусто)
                $this->isInstanceOf(\DateTimeImmutable::class), // planting_date
                'ЦитрусМикс',                        // seller
                'Италия',                            // nursery
                $this->isInstanceOf(Price::class),   // price
                $this->isInstanceOf(Price::class),   // shipping_cost
                null,                                // packaging_cost (пусто)
                'Для цитрусовых',                    // soil
                false,                               // str2bool('false')
                null,                                // selling_date
                null,                                // selling_price
                'Требует много света'                // comment
            )
            ->willReturn($mockModel);

        // 3. Проверка передачи в сервис
        $this->plantService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testPriceTransformation(): void
    {
        $data = [
            'group_id' => '1', 'title' => 'T', 'room' => 'R', 'is_shown' => '1', 'description' => '',
            'purchase_date' => '', 'vaccination_date' => '', 'planting_date' => '',
            'seller' => '', 'nursery' => '',
            'price' => '500 USD', // Проверяем конкретную цену
            'shipping_cost' => '', 'packaging_cost' => '', 'soil' => '',
            'is_sold' => '0', 'selling_date' => '', 'selling_price' => '', 'comment' => ''
        ];

        $this->modelFactory->method('makeModel')->willReturnCallback(function ($class, ...$args) {
            // Аргумент под индексом 10 (11-й по счету) — это price
            $price = $args[10];
            $this->assertInstanceOf(Price::class, $price);
            $this->assertEquals('500 USD', (string)$price);
            return $this->createMock(CreatePlantModel::class);
        });

        $this->importer->import($data);
    }
}
