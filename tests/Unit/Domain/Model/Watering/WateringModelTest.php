<?php

namespace Unit\Domain\Model\Watering;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use App\Domain\Entity\Watering;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Watering\WateringModel;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use App\Domain\ValueObject\Watering\WateringDetails;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

class WateringModelTest extends TestCase
{
    public function testFromEntityAndToArray(): void
    {
        $timezone = new DateTimeZone('Europe/Moscow');
        $now = new DateTimeImmutable('2024-03-10 14:00:00', $timezone);

        // 1. Подготавливаем Enums (берем первые доступные кейсы)
        $waterType = WaterType::cases()[0];
        $wateringMethod = WateringMethod::cases()[0];

        // 2. Создаем реальный VO деталей полива
        $details = new WateringDetails(
            250,
            $waterType,
            $wateringMethod,
            '25°C'
        );

        // 3. Мокаем сущности
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        $markerEntity = $this->createMock(Marker::class);
        $markerEntity->method('getId')->willReturn(5);

        $watering = $this->createMock(Watering::class);
        $watering->method('getId')->willReturn(100);
        $watering->method('getGroup')->willReturn($groupEntity);
        $watering->method('getMarker')->willReturn($markerEntity);
        $watering->method('getDetails')->willReturn($details);
        $watering->method('getDescription')->willReturn('Regular watering');
        $watering->method('getComment')->willReturn('Add fertilizer next time');
        $watering->method('getCreatedAt')->willReturn($now);
        $watering->method('getUpdatedAt')->willReturn($now);

        // 4. Мокаем модели (DTO)
        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Succulents');

        $markerModel = $this->createMock(MarkerModel::class);
        $markerModel->method('getLetter')->willReturn('W');
        $markerModel->method('getColor')->willReturn('#0000FF');

        // 5. Тестируем фабричный метод
        $model = WateringModel::fromEntity($watering, $groupModel, $markerModel);

        $this->assertEquals(100, $model->getId());
        $this->assertEquals(250, $model->getAmount());

        // 6. Тестируем toArray()
        $array = $model->toArray();

        $this->assertEquals('Succulents', $array['group_title']);
        $this->assertEquals('W', $array['marker_letter']);
        $this->assertEquals($waterType->getLabel(), $array['water_type']);
        $this->assertEquals($wateringMethod->getLabel(), $array['watering_method']);
        $this->assertEquals('10.03.2024 14:00:00', $array['created_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = WateringModel::getTableHeaderRu();
        $this->assertEquals('table.watering.header.water_type', $headers['water_type']);
        $this->assertArrayHasKey('marker_letter', $headers);
    }
}
