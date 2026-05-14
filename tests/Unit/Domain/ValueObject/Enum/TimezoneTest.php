<?php

namespace Unit\Domain\ValueObject\Enum;

use Exception;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Timezone;

class TimezoneTest extends TestCase
{
    public function testLabelReturnsCorrectRussianName(): void
    {
        $this->assertEquals('timezone.moscow', Timezone::Moscow->labelKey());
        $this->assertEquals('timezone.new_york', Timezone::NewYork->labelKey());
        $this->assertEquals('timezone.utc', Timezone::Utc->labelKey());
        $this->assertEquals('timezone.kamchatka', Timezone::Kamchatka->labelKey());
    }

    public function testGetChoicesContainsAllLabelsAndValues(): void
    {
        $choices = Timezone::getChoices();

        // Проверяем общее количество (сейчас в Enum 26 кейсов)
        $this->assertCount(26, $choices);

        // Проверяем конкретные пары
        $this->assertArrayHasKey('timezone.los_angeles', $choices);
        $this->assertEquals('America/Los_Angeles', $choices['timezone.los_angeles']);

        $this->assertArrayHasKey('timezone.tokyo', $choices);
        $this->assertEquals('Asia/Tokyo', $choices['timezone.tokyo']);
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (Timezone::cases() as $case) {
            // Проверка, что match покрывает все кейсы и не возвращает пустую строку
            $this->assertNotEmpty($case->labelKey(), "Отсутствует label для временной зоны: {$case->value}");
        }
    }

    public function testValuesAreValidTimezoneIdentifiers(): void
    {
        foreach (Timezone::cases() as $case) {
            // Если часовой пояс некорректен, конструктор DateTimeZone выбросит исключение
            try {
                new DateTimeZone($case->value);
                $this->assertTrue(true);
            } catch (Exception $e) {
                $this->fail("Некорректный идентификатор временной зоны: {$case->value}");
            }
        }
    }
}
