<?php

namespace Unit\Domain\ValueObject\Offspring;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Offspring\FruitMetrics;

class FruitMetricsTest extends TestCase
{
    public function testInitializationWithFullData(): void
    {
        $mass = 150;
        $quantity = 5;
        $color = 'Красный';
        $flavor = 'Сладкий с кислинкой';

        $metrics = new FruitMetrics($mass, $quantity, $color, $flavor);

        $this->assertEquals($mass, $metrics->getMass());
        $this->assertEquals($quantity, $metrics->getQuantity());
        $this->assertEquals($color, $metrics->getColor());
        $this->assertEquals($flavor, $metrics->getFlavor());
    }

    public function testEmptyInitialization(): void
    {
        $metrics = new FruitMetrics();

        $this->assertNull($metrics->getMass());
        $this->assertNull($metrics->getQuantity());
        $this->assertNull($metrics->getColor());
        $this->assertNull($metrics->getFlavor());
    }

    public function testPartialInitialization(): void
    {
        // Проверяем только массу и цвет
        $metrics = new FruitMetrics(mass: 200, color: 'Желтый');

        $this->assertEquals(200, $metrics->getMass());
        $this->assertEquals('Желтый', $metrics->getColor());
        $this->assertNull($metrics->getQuantity());
        $this->assertNull($metrics->getFlavor());
    }

    public function testMassAndQuantityTypes(): void
    {
        $metrics = new FruitMetrics(100, 1);

        $this->assertIsInt($metrics->getMass());
        $this->assertIsInt($metrics->getQuantity());
    }
}
