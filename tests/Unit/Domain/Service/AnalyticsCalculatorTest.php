<?php

namespace Unit\Domain\Service;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\AnalyticsCalculator;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AnalyticsCalculator::class)]
class AnalyticsCalculatorTest extends TestCase
{
    private AnalyticsCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new AnalyticsCalculator();
    }

    #[Test]
    public function calculateNewAverageReturnsValueAsIsIfCountIsZero(): void
    {
        $result = $this->calculator->calculateNewAverage(0.0, 0, 15.0);
        $this->assertEquals(15.0, $result);
    }

    #[Test]
    public function calculateNewAverageCalculatesCorrectMovingAverage(): void
    {
        // Дано: среднее 10 за 2 итерации. Добавляем значение 16 (итерация 3).
        // Формула: 10 + (16 - 10) / 3 = 10 + 2 = 12
        $result = $this->calculator->calculateNewAverage(10.0, 3, 16.0);

        $this->assertEquals(12.0, $result);
    }

    #[Test]
    public function predictNextDateAddsCorrectNumberOfDays(): void
    {
        $lastDate = new DateTimeImmutable('2024-01-01');
        $averageInterval = 4.4; // Должно округлиться до 4 дней

        $nextDate = $this->calculator->predictNextDate($lastDate, $averageInterval);

        $this->assertEquals('2024-01-05', $nextDate->format('Y-m-d'));
    }

    #[Test]
    public function predictNextDateRoundsUpInterval(): void
    {
        $lastDate = new DateTimeImmutable('2024-01-01');
        $averageInterval = 4.6; // Должно округлиться до 5 дней

        $nextDate = $this->calculator->predictNextDate($lastDate, $averageInterval);

        $this->assertEquals('2024-01-06', $nextDate->format('Y-m-d'));
    }
}
