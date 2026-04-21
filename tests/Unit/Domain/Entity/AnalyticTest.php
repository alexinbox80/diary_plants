<?php

namespace Unit\Domain\Entity;

use App\Domain\Entity\Group;
use App\Domain\Entity\Plant;
use App\Domain\Entity\Analytic;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Domain\ValueObject\Analytic\IntervalMetrics;

#[CoversClass(Analytic::class)]
class AnalyticTest extends TestCase
{
    #[Test]
    public function constructorInitializesEntityCorrectly(): void
    {
        // 1. Arrange
        $plant = $this->createMock(Plant::class);
        $group = $this->createMock(Group::class);

        // 2. Act
        $analytic = new Analytic($plant, $group);

        // 3. Assert
        $this->assertSame($plant, $analytic->getPlant());
        $this->assertSame($group, $analytic->getGroup());

        // Проверяем, что метрики инициализированы значениями по умолчанию
        $metrics = $analytic->getWateringMetrics();
        $this->assertInstanceOf(IntervalMetrics::class, $metrics);
        $this->assertEquals(0, $metrics->getCount());
        $this->assertEquals(0.0, $metrics->getAverageDays());
    }

    #[Test]
    public function changeWateringMetricsUpdatesData(): void
    {
        // 1. Arrange
        $analytic = new Analytic(
            $this->createMock(Plant::class),
            $this->createMock(Group::class)
        );
        $newMetrics = new IntervalMetrics(10, 5.5);

        // 2. Act
        $analytic->changeWateringMetrics($newMetrics);

        // 3. Assert
        $this->assertSame($newMetrics, $analytic->getWateringMetrics());
        $this->assertEquals(10, $analytic->getWateringMetrics()->getCount());
        $this->assertEquals(5.5, $analytic->getWateringMetrics()->getAverageDays());
    }

    #[Test]
    public function getIdThrowsExceptionWhenIdIsNull(): void
    {
        $analytic = new Analytic(
            $this->createMock(Plant::class),
            $this->createMock(Group::class)
        );

        $this->expectException(\InvalidArgumentException::class);
        // Сообщение из вашего WebmozartAssert
        $this->expectExceptionMessage('Id of Entity App\Domain\Entity\Analytic is null.');

        $analytic->getId();
    }
}
