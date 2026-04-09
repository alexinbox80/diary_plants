<?php

namespace Unit\Domain\Model\Usage;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Usage\UsageModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\ValueObject\Usage\AttachableReference;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class UsageModelTest extends TestCase
{
    public function testFromEntityAndTransformations(): void
    {
        $tz = new DateTimeZone('Europe/Moscow');
        $useDate = new DateTimeImmutable('2024-06-15 10:00:00', $tz);
        $now = new DateTimeImmutable('2024-06-15 12:00:00', $tz);

        // 1. Создаем РЕАЛЬНЫЙ Value Object вместо анонимного класса
        $target = new AttachableReference(
            55,
            AttachableType::STIMULANT
        );

        // 2. Мокаем сущности (Entity)
        $groupEntity = $this->createMock(Group::class);
        $groupEntity->method('getId')->willReturn(1);

        $plantEntity = $this->createMock(Plant::class);
        $plantEntity->method('getId')->willReturn(10);

        $usage = $this->createMock(Usage::class);
        $usage->method('getId')->willReturn(100);
        $usage->method('getGroup')->willReturn($groupEntity);
        $usage->method('getPlant')->willReturn($plantEntity);
        $usage->method('getUseDate')->willReturn($useDate);
        $usage->method('getTarget')->willReturn($target); // Теперь возвращаем реальный VO
        $usage->method('getComment')->willReturn('Test comment');
        $usage->method('getCreatedAt')->willReturn($now);
        $usage->method('getUpdatedAt')->willReturn($now);

        // 3. Мокаем связанные модели (DTO)
        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Main Group');

        $plantModel = $this->createMock(PlantModel::class);
        $plantModel->method('getTitle')->willReturn('Ficus');

        $attachable = $this->createMock(AttachableModelInterface::class);

        // 4. Вызов тестируемого метода
        $model = UsageModel::fromEntity($usage, $groupModel, $plantModel, $attachable);

        // 5. Проверки toArray()
        $array = $model->toArray();
        $this->assertEquals('15.06.2024', $array['use_date']);
        $this->assertEquals('Ficus', $array['plant_title']);
        $this->assertEquals(100, $array['id']);

        // 6. Проверки toJson() (логика календаря)
        $json = $model->toJson();
        $this->assertEquals(100, $json['base_id']);
        $this->assertEquals('2024-06-15', $json['date']);

        // Формула cell_id: usableType + '-' + (plantId * 100 + day)
        // 'stimulant-1015' (т.к. plantId=10, day=15)
        $expectedCellId = AttachableType::STIMULANT->value . '-1015';
        $this->assertEquals($expectedCellId, $json['cell_id']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = UsageModel::getTableHeaderRu();
        $this->assertEquals('Дата использования', $headers['use_date']);
        $this->assertArrayHasKey('usable_type', $headers);
    }
}
