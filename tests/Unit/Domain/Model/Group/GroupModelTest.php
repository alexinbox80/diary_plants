<?php

namespace Unit\Domain\Model\Group;

namespace App\Tests\Unit\Domain\Model\Group;

use App\Domain\Entity\Group;
use App\Domain\Model\Group\GroupModel;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;
use DateTimeZone;

class GroupModelTest extends TestCase
{
    /**
     * Проверка создания модели из сущности
     */
    public function testFromEntityMapping(): void
    {
        $createdAt = new DateTimeImmutable('2024-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-01 11:00:00');

        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(1);
        $group->method('getTitle')->willReturn('Суккуленты');
        $group->method('isActive')->willReturn(true);
        $group->method('getDescription')->willReturn('Растения пустыни');
        $group->method('getCreatedAt')->willReturn($createdAt);
        $group->method('getUpdatedAt')->willReturn($updatedAt);

        $model = GroupModel::fromEntity($group);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Суккуленты', $model->getTitle());
        $this->assertTrue($model->isActive());
        $this->assertEquals('Растения пустыни', $model->getDescription());
        $this->assertSame($createdAt, $model->getCreatedAt());
    }

    /**
     * Проверка форматирования массива для фронтенда
     */
    public function testToArrayFormatting(): void
    {
        $utcTime = new DateTimeImmutable('2024-01-01 12:00:00', new DateTimeZone('UTC'));

        $model = new GroupModel(
            id: 5,
            title: 'Тропики',
            isActive: false,
            description: 'Влажный климат',
            createdAt: $utcTime,
            updatedAt: $utcTime
        );

        $result = $model->toArray();

        $this->assertEquals(5, $result['id']);
        $this->assertEquals(0, $result['is_active']); // bool -> string
        $this->assertEquals('Тропики', $result['title']);
        // Проверка конвертации таймзоны UTC 12:00 -> MSK 15:00
        $this->assertEquals('01.01.2024 15:00:00', $result['created_at']);
    }

    public function testGetTableHeaderRu(): void
    {
        $headers = GroupModel::getTableHeaderRu();

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('is_active', $headers);
        $this->assertEquals('table.group.header.is_active', $headers['is_active']);
    }
}

