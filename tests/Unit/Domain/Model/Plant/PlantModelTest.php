<?php

namespace Unit\Domain\Model\Plant;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\ValueObject\Plant\LifeCycle;
use App\Domain\ValueObject\Plant\SalesInfo;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\Model\Offspring\OffspringModel;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use App\Domain\ValueObject\Plant\PlantIdentifier;

#[CoversClass(PlantModel::class)]
class PlantModelTest extends TestCase
{
    #[Test]
    public function fromEntityCreatesValidModelWithAllCollections(): void
    {
        $now = new DateTimeImmutable('2024-01-01 12:00:00');
        $oid = OId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $price = new Price(1000, Currency::RUR);

        // 1. Подготовка моков компонентов сущности (Value Objects)
        $plantIdentifier = $this->createMock(PlantIdentifier::class);
        $plantIdentifier->method('getOid')->willReturn($oid);
        $plantIdentifier->method('getQrCodeLink')->willReturn('https://qr.link');

        $purchaseInfo = $this->createMock(PurchaseInfo::class);
        $purchaseInfo->method('getPurchaseDate')->willReturn($now);
        $purchaseInfo->method('getPrice')->willReturn($price);
        $purchaseInfo->method('getSeller')->willReturn('Green Shop');

        $lifeCycle = $this->createMock(LifeCycle::class);
        $lifeCycle->method('getPlantingDate')->willReturn($now);
        $lifeCycle->method('getSoil')->willReturn('Universal');

        $salesInfo = $this->createMock(SalesInfo::class);
        $salesInfo->method('isSold')->willReturn(false);

        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        // 2. Мок основной сущности Plant
        $plant = $this->createMock(Plant::class);
        $plant->method('getId')->willReturn(100);
        $plant->method('getTitle')->willReturn('Ficus');
        $plant->method('getRoom')->willReturn('Office');
        $plant->method('isShown')->willReturn(true);
        $plant->method('getGroup')->willReturn($groupEntity);
        $plant->method('getPlantIdentifier')->willReturn($plantIdentifier);
        $plant->method('getPurchaseInfo')->willReturn($purchaseInfo);
        $plant->method('getLifeCycle')->willReturn($lifeCycle);
        $plant->method('getSalesInfo')->willReturn($salesInfo);
        $plant->method('getCreatedAt')->willReturn($now);
        $plant->method('getUpdatedAt')->willReturn($now);

        // 3. Создаем моки для вложенных моделей (Usage, Offspring, Repotting)
        $usageModel = $this->createMock(UsageModel::class);
        $usageModel->method('toArray')->willReturn(['id' => 1, 'comment' => 'Watering']);

        $offspringModel = $this->createMock(OffspringModel::class);
        $offspringModel->method('toArray')->willReturn(['id' => 10, 'comment' => 'Child']);

        $repottingModel = $this->createMock(RepottingModel::class);
        $repottingModel->method('toArray')->willReturn(['id' => 20, 'substrate' => 'Peat']);

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Ficus Group');

        // 4. Вызываем тестируемый метод fromEntity
        $model = PlantModel::fromEntity(
            $plant,
            [],              // attachmentModels
            $groupModel,
            [$usageModel],   // usageModels
            [$offspringModel],// offspringModels
            [$repottingModel] // repottingModels
        );

        // 5. Проверки структуры модели (Assertions)
        $this->assertEquals(100, $model->getId());
        $this->assertEquals('Ficus', $model->getTitle());
        $this->assertCount(1, $model->getUsage());
        $this->assertCount(1, $model->getOffspring());
        $this->assertCount(1, $model->getRepotting());
        $this->assertSame($usageModel, $model->getUsage()[0]);

        // 6. Проверка финального массива (toArray)
        $array = $model->toArray();

        $this->assertArrayHasKey('usages', $array);
        $this->assertArrayHasKey('offsprings', $array);
        $this->assertArrayHasKey('repottings', $array);

        // Проверяем, что данные из моков попали в итоговый массив через toArray()
        $this->assertEquals('Watering', $array['usages'][0]['comment']);
        $this->assertEquals('Child', $array['offsprings'][0]['comment']);
        $this->assertEquals('Peat', $array['repottings'][0]['substrate']);
        $this->assertEquals('Да', $array['is_shown']);
    }

    #[Test]
    public function tableHeaderRuContainsAllKeys(): void
    {
        $headers = PlantModel::getTableHeaderRu();

        $expectedKeys = [
            'id', 'group_id', 'group_title', 'oid', 'title', 'room',
            'is_shown', 'purchase_date', 'price', 'created_at'
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $headers);
        }
        $this->assertEquals('Название', $headers['title']);
    }
}
