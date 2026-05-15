<?php

namespace Unit\Domain\ValueObject\Enum\Repotting;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;

class PotMaterialTest extends TestCase
{
    public function testGetLabelReturnsCorrectRussianTranslation(): void
    {
        $this->assertEquals('pot_material.plastic', PotMaterial::PLASTIC->getLabelKey());
        $this->assertEquals('pot_material.ceramic', PotMaterial::CERAMIC->getLabelKey());
        $this->assertEquals('pot_material.terracotta', PotMaterial::TERRACOTTA->getLabelKey());
        $this->assertEquals('pot_material.textile', PotMaterial::TEXTILE->getLabelKey());
        $this->assertEquals('pot_material.peat', PotMaterial::PEAT->getLabelKey());
    }

    public function testGetChoicesReturnsFormattedArray(): void
    {
        $choices = PotMaterial::getChoices();

        $expected = [
            'pot_material.plastic' => 'plastic',
            'pot_material.ceramic' => 'ceramic',
            'pot_material.terracotta' => 'terra',
            'pot_material.textile' => 'textile',
            'pot_material.peat' => 'peat',
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
