<?php

namespace Unit\Domain\ValueObject\Enum\Watering;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

class WateringMethodTest extends TestCase
{
    public function testGetLabelReturnsRussianTranslation(): void
    {
        $this->assertEquals('Верхний полив', WateringMethod::TOP->getLabel());
        $this->assertEquals('Погружение', WateringMethod::IMMERSION->getLabel());
        $this->assertEquals('Фитильный', WateringMethod::WICK->getLabel());
    }

    public function testGetValuesReturnsAllRawStrings(): void
    {
        $expected = ['top', 'bottom', 'immersion', 'spray', 'wick'];

        $this->assertEquals($expected, WateringMethod::getValues());
    }

    public function testAsSelectArrayReturnsCorrectStructure(): void
    {
        $result = WateringMethod::asSelectArray();

        $this->assertArrayHasKey('В поддон', $result);
        $this->assertEquals('bottom', $result['В поддон']);

        $this->assertArrayHasKey('Опрыскивание', $result);
        $this->assertEquals('spray', $result['Опрыскивание']);

        $this->assertCount(5, $result);
    }

    public function testEnumCompleteness(): void
    {
        // Проверка, что для каждого кейса прописан label (защита от забытого match)
        foreach (WateringMethod::cases() as $case) {
            $this->assertIsString($case->getLabel());
            $this->assertNotEmpty($case->getLabel());
        }
    }
}

