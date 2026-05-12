<?php

namespace Unit\Domain\Model\Marker;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Marker;
use PHPUnit\Framework\TestCase;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

class MarkerModelTest extends TestCase
{
    /**
     * Проверяем создание модели из сущности
     */
    public function testFromEntityMapping(): void
    {
        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(7);

        $marker = $this->createMock(Marker::class);
        $marker->method('getId')->willReturn(100);
        $marker->method('getGroup')->willReturn($group);
        $marker->method('getLetter')->willReturn('NPK');
        $marker->method('getColor')->willReturn('#FF0000');
        $marker->method('getType')->willReturn(AttachableType::FERTILIZER);
        $marker->method('getDescription')->willReturn('Main fertilizer');
        $marker->method('getColorDescription')->willReturn('Red color');
        $marker->method('getCreatedAt')->willReturn(new DateTimeImmutable('2024-01-01 10:00:00'));
        $marker->method('getUpdatedAt')->willReturn(new DateTimeImmutable('2024-01-01 11:00:00'));

        $model = MarkerModel::fromEntity($marker);

        $this->assertEquals(100, $model->getId());
        $this->assertEquals(7, $model->getGroupId());
        $this->assertEquals('NPK', $model->getLetter());
        $this->assertEquals('#FF0000', $model->getColor());
        $this->assertEquals('fertilizer', $model->getType()); // value из Enum
        $this->assertEquals('Main fertilizer', $model->getDescription());
    }

    /**
     * Проверка форматирования массива и локализации типа
     */
    public function testToArrayFormatting(): void
    {
        $date = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));

        $groupModel = $this->createMock(GroupModel::class);
        $groupModel->method('getTitle')->willReturn('Base Group');

        $model = new MarkerModel(
            id: 1,
            groupId: 7,
            letter: 'H2O',
            color: '#0000FF',
            type: 'watering',
            description: 'Regular watering',
            colorDescription: 'Blue',
            group: $groupModel,
            createdAt: $date,
            updatedAt: $date
        );

        $result = $model->toArray();

        // Проверка локализации типа через AttachableType::getLabel()
        $this->assertEquals('Полив', $result['type']);

        // Проверка конвертации таймзоны UTC 12:00 -> MSK 15:00
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);

        // Проверка связи с GroupModel
        $this->assertEquals('Base Group', $result['group_title']);
    }

    /**
     * Проверка структуры заголовков
     */
    public function testGetTableHeaderRu(): void
    {
        $headers = MarkerModel::getTableHeaderRu();
        $this->assertEquals('table.marker.header.letter', $headers['letter']);
        $this->assertEquals('table.marker.header.type', $headers['type']);
    }
}
