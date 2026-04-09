<?php

namespace Unit\Domain\ValueObject\Enum\Watering;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Watering\WaterType;

class WaterTypeTest extends TestCase
{
    public function testGetLabelReturnsCorrectRussianName(): void
    {
        $this->assertEquals('Отстоянная', WaterType::SETTLED->getLabel());
        $this->assertEquals('Осмос', WaterType::OSMOSIS->getLabel());
        $this->assertEquals('Дождевая', WaterType::RAIN->getLabel());
    }

    public function testGetValuesReturnsAllDatabaseStrings(): void
    {
        $expected = ['settled', 'filtered', 'tap', 'distilled', 'rain', 'osmosis'];

        $this->assertEquals($expected, WaterType::getValues());
    }

    public function testAsSelectArrayReturnsValidMap(): void
    {
        $options = WaterType::asSelectArray();

        $this->assertCount(6, $options);
        $this->assertEquals('filtered', $options['Фильтрованная']);
        $this->assertEquals('tap', $options['Водопроводная']);
        $this->assertEquals('distilled', $options['Дистиллированная']);
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (WaterType::cases() as $case) {
            $this->assertNotEmpty($case->getLabel(), "У кейса {$case->name} отсутствует метка");
        }
    }
}
