<?php

namespace Unit\Domain\ValueObject\Enum\Repotting;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class RepottingTypeTest extends TestCase
{
    public function testGetLabelReturnsCorrectRussianTranslation(): void
    {
        $this->assertEquals('Перевалка', RepottingType::POTTING_UP->getLabel());
        $this->assertEquals('Реанимационная пересадка', RepottingType::EMERGENCY->getLabel());
        $this->assertEquals('Пикировка', RepottingType::PRICKING_OUT->getLabel());
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
        $this->assertArrayHasKey('Замена верхнего слоя', $choices);
        $this->assertEquals('top_dressing', $choices['Замена верхнего слоя']);

        $this->assertArrayHasKey('Посадка саженца', $choices);
        $this->assertEquals('planting', $choices['Посадка саженца']);
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
            $this->assertNotEmpty($case->getLabel(), "Label is missing for case: " . $case->value);
            $this->assertNotEmpty($case->getDescription(), "Description is missing for case: " . $case->value);
        }
    }
}
