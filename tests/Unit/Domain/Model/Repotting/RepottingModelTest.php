<?php

namespace Unit\Domain\Model\Repotting;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Repotting;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Repotting\RepottingModel;
use App\Domain\ValueObject\Repotting\RepottingDetails;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;


class RepottingModelTest extends TestCase
{
    public function testFromEntityAndToArray(): void
    {
        $now = new DateTimeImmutable('2024-05-20 10:00:00');

        // 1. Подготавливаем Enums
        $type = RepottingType::cases()[0];
        $material = PotMaterial::cases()[0];

        // 2. Создаем РЕАЛЬНЫЙ объект деталей, так как мок требует соблюдения типа
        $details = new RepottingDetails(
            $type,
            $material,
            '12cm'
        );

        // 3. Мокаем связанные сущности
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        $plantEntity = $this->createMock(Plant::class);
        $plantEntity->method('getId')->willReturn(10);

        // 4. Мокаем основную сущность Repotting
        $repotting = $this->createMock(Repotting::class);
        $repotting->method('getId')->willReturn(100);
        $repotting->method('getGroup')->willReturn($groupEntity);
        $repotting->method('getPlant')->willReturn($plantEntity);
        $repotting->method('getRepottedAt')->willReturn($now);
        $repotting->method('getDetails')->willReturn($details);
        $repotting->method('getComment')->willReturn('Excellent');
        $repotting->method('getCreatedAt')->willReturn($now);
        $repotting->method('getUpdatedAt')->willReturn($now);

        // 5. Мокаем модели для DTO
        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Succulents');

        $plantModel = $this->createMock(PlantModel::class);
        $plantModel->method('getTitle')->willReturn('Aloe');

        // Выполнение
        $model = RepottingModel::fromEntity($repotting, $groupModel, $plantModel);

        // Проверки
        $this->assertEquals(100, $model->getId());

        $array = $model->toArray();
        $this->assertEquals('Succulents', $array['group_title']);
        $this->assertEquals('20.05.2024', $array['repotted_at']);
        $this->assertEquals($type->getLabel(), $array['type']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = RepottingModel::getTableHeaderRu();
        $this->assertArrayHasKey('repotted_at', $headers);
        $this->assertEquals('Дата пересадки', $headers['repotted_at']);
    }
}
