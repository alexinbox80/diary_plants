<?php

namespace Unit\Domain\Model\Analytic;

use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Analytic;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Model\Analytic\AnalyticModel;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Analytic\IntervalMetrics;

#[CoversClass(AnalyticModel::class)]
class AnalyticModelTest extends TestCase
{
    #[Test]
    public function testConstructorAndGetters(): void
    {
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-01-02');

        $model = new AnalyticModel(
            id: 1,
            plantId: 10,
            groupId: 5,
            count: 3,
            averageDays: 7.5,
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );

        $this->assertSame(1, $model->getId());
        $this->assertSame(10, $model->getPlantId());
        $this->assertSame(5, $model->getGroupId());
        $this->assertSame(3, $model->getCount());
        $this->assertSame(7.5, $model->getAverageDays());
        $this->assertSame($createdAt, $model->getCreatedAt());
        $this->assertSame($updatedAt, $model->getUpdatedAt());
    }

    #[Test]
    public function testFromEntity(): void
    {
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-01-02');

        // Мокаем вложенные объекты
        $plant = $this->createMock(Plant::class);
        $plant->method('getId')->willReturn(10);

        $group = $this->createMock(Group::class);
        $group->method('getId')->willReturn(5);

        $metrics = $this->createMock(IntervalMetrics::class);
        $metrics->method('getCount')->willReturn(3);
        $metrics->method('getAverageDays')->willReturn(7.5);

        // Мокаем основную сущность Analytic
        $analytic = $this->createMock(Analytic::class);
        $analytic->method('getId')->willReturn(1);
        $analytic->method('getPlant')->willReturn($plant);
        $analytic->method('getGroup')->willReturn($group);
        $analytic->method('getWateringMetrics')->willReturn($metrics);
        $analytic->method('getCreatedAt')->willReturn($createdAt);
        $analytic->method('getUpdatedAt')->willReturn($updatedAt);

        $model = AnalyticModel::fromEntity($analytic);

        $this->assertInstanceOf(AnalyticModel::class, $model);
        $this->assertSame(1, $model->getId());
        $this->assertSame(10, $model->getPlantId());
        $this->assertSame(5, $model->getGroupId());
        $this->assertSame(3, $model->getCount());
        $this->assertSame(7.5, $model->getAverageDays());
    }
}
