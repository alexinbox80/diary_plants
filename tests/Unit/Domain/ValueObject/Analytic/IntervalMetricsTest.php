<?php

namespace Unit\Domain\ValueObject\Analytic;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Service\AnalyticService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Domain\ValueObject\Analytic\IntervalMetrics;

#[CoversClass(AnalyticService::class)]
class IntervalMetricsTest extends TestCase
{
    #[Test]
    public function testValidCreation(): void
    {
        $count = 10;
        $average = 3.5;

        $metrics = new IntervalMetrics($count, $average);

        $this->assertEquals($count, $metrics->getCount());
        $this->assertEquals($average, $metrics->getAverageDays());
    }

    #[Test]
    public function testDefaultValues(): void
    {
        $metrics = new IntervalMetrics();

        $this->assertSame(0, $metrics->getCount());
        $this->assertSame(0.0, $metrics->getAverageDays());
    }

    #[Test]
    #[DataProvider('invalidValuesProvider')]
    public function testValidationThrowsException(int $count, float $average): void
    {
        $this->expectException(InvalidArgumentException::class);

        new IntervalMetrics($count, $average);
    }

    public static function invalidValuesProvider(): array
    {
        return [
            'negative count' => [-1, 5.0],
            'negative average' => [5, -1.5],
            'both negative' => [-10, -0.1],
        ];
    }
}
