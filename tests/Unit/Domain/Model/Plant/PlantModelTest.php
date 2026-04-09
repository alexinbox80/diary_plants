<?php

namespace Unit\Domain\Model\Plant;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Enum\Currency;
use App\Domain\ValueObject\Plant\LifeCycle;
use App\Domain\ValueObject\Plant\SalesInfo;
use App\Domain\ValueObject\Plant\PurchaseInfo;
use App\Domain\ValueObject\Plant\PlantIdentifier;

class PlantModelTest extends TestCase
{
    public function testFromEntityCreatesValidModel(): void
    {
        $now = new DateTimeImmutable('2024-01-01 12:00:00');

        // 1. Создаем реальные Value Objects
        $oid = OId::fromString('550e8400-e29b-41d4-a716-446655440000');

        // Создаем Price с учетом требований конструктора (сумма и Enum валюты)
        // Если у тебя в Price есть именованный конструктор, например Price::create(1000, Currency::RUB) — используй его
        $price = new Price(1000, Currency::RUR);

        // 2. Мокаем компоненты сущности (как и раньше)
        $plantIdentifier = $this->createMock(PlantIdentifier::class);
        $plantIdentifier->method('getOid')->willReturn($oid);
        $plantIdentifier->method('getQrCodeLink')->willReturn('https://qr.link');

        $purchaseInfo = $this->createMock(PurchaseInfo::class);
        $purchaseInfo->method('getPurchaseDate')->willReturn($now);
        $purchaseInfo->method('getPrice')->willReturn($price);
        $purchaseInfo->method('getShippingCost')->willReturn(null);
        $purchaseInfo->method('getPackagingCost')->willReturn(null);
        $purchaseInfo->method('getSeller')->willReturn('Green Shop');

        $lifeCycle = $this->createMock(LifeCycle::class);
        $lifeCycle->method('getPlantingDate')->willReturn($now);
        $lifeCycle->method('getSoil')->willReturn('Universal');

        $salesInfo = $this->createMock(SalesInfo::class);
        $salesInfo->method('isSold')->willReturn(false);

        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        // 3. Мокаем основную сущность
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

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Ficus Group');

        // 4. Вызов и проверки
        $model = PlantModel::fromEntity($plant, [], $groupModel);

        $this->assertEquals(100, $model->getId());
        $this->assertEquals('550e8400-e29b-41d4-a716-446655440000', $model->getOid()->toString());

        $array = $model->toArray();
        $this->assertEquals('Да', $array['is_shown']);
        $this->assertEquals('01.01.2024', $array['purchase_date']);
        // Здесь проверка зависит от того, что возвращает Price::toString()
        $this->assertStringContainsString('1000', $array['price']);
    }

    public function testTableHeaderRuContainsAllKeys(): void
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
