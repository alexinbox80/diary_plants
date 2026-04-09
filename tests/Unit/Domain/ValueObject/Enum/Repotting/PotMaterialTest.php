<?php

namespace Unit\Domain\ValueObject\Enum\Repotting;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;

class PotMaterialTest extends TestCase
{
    public function testGetLabelReturnsCorrectRussianTranslation(): void
    {
        $this->assertEquals('Пластик', PotMaterial::PLASTIC->getLabel());
        $this->assertEquals('Керамика', PotMaterial::CERAMIC->getLabel());
        $this->assertEquals('Терракота (глина)', PotMaterial::TERRACOTTA->getLabel());
        $this->assertEquals('Текстильный мешок', PotMaterial::TEXTILE->getLabel());
        $this->assertEquals('Торфяной стаканчик', PotMaterial::PEAT->getLabel());
    }

    public function testGetChoicesReturnsFormattedArray(): void
    {
        $choices = PotMaterial::getChoices();

        $expected = [
            'Пластик' => 'plastic',
            'Керамика' => 'ceramic',
            'Терракота (глина)' => 'terra',
            'Текстильный мешок' => 'textile',
            'Торфяной стаканчик' => 'peat',
        ];

        $this->assertSame($expected, $choices);
        $this->assertCount(5, $choices);
    }

    public function testValuesReturnsAllRawValues(): void
    {
        $expectedValues = ['plastic', 'ceramic', 'terra', 'textile', 'peat'];

        $this->assertEquals($expectedValues, PotMaterial::values());
    }

    public function testEnumCasesCount(): void
    {
        // Гарантируем, что при добавлении нового кейса мы не забудем обновить тесты
        $this->assertCount(5, PotMaterial::cases());
    }
}
