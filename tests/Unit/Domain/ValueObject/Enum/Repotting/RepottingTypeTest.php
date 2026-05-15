<?php

namespace Unit\Domain\ValueObject\Enum\Repotting;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class RepottingTypeTest extends TestCase
{
    public function testGetLabelReturnsCorrectRussianTranslation(): void
    {
        $this->assertEquals('repot_type.potting_up', RepottingType::POTTING_UP->getLabelKey());
        $this->assertEquals('repot_type.emergency', RepottingType::EMERGENCY->getLabelKey());
        $this->assertEquals('repot_type.pricking_out', RepottingType::PRICKING_OUT->getLabelKey());
    }

    public function testGetDescriptionReturnsCorrectInformation(): void
    {
        $this->assertStringContainsString('земляного кома', RepottingType::POTTING_UP->getDescription());
        $this->assertStringContainsString('гнили или вредителей', RepottingType::EMERGENCY->getDescription());
        $this->assertStringContainsString('бонсай', RepottingType::ROOT_PRUNING->getDescription());
    }

    public function testGetChoicesFormat(): void
    {
        $choices = RepottingType::getChoices();

        // Проверяем наличие ключевых элементов
        $this->assertArrayHasKey('repot_type.top_dressing', $choices);
        $this->assertEquals('top_dressing', $choices['repot_type.top_dressing']);

        $this->assertArrayHasKey('repot_type.planting', $choices);
        $this->assertEquals('planting', $choices['repot_type.planting']);
    }

    public function testValuesMethodReturnsAllKeys(): void
    {
        $values = RepottingType::values();

        $this->assertCount(8, $values);
        $this->assertContains('division', $values);
        $this->assertContains('repot_full', $values);
    }

    /**
     * Проверка, что для каждого кейса определено описание и заголовок
     */
    public function testAllCasesHaveLabelsAndDescriptions(): void
    {
        foreach (RepottingType::cases() as $case) {
            $this->assertNotEmpty($case->getLabelKey(), "Label is missing for case: " . $case->value);
            $this->assertNotEmpty($case->getDescription(), "Description is missing for case: " . $case->value);
        }
    }
}
