<?php

namespace Unit\Domain\ValueObject\Enum;

use PHPUnit\Framework\TestCase;
use App\Domain\ValueObject\Enum\Timezone;

class TimezoneTest extends TestCase
{
    public function testLabelReturnsCorrectRussianName(): void
    {
        $this->assertEquals('Москва', Timezone::Moscow->label());
        $this->assertEquals('Нью-Йорк', Timezone::NewYork->label());
        $this->assertEquals('UTC', Timezone::Utc->label());
        $this->assertEquals('Камчатка', Timezone::Kamchatka->label());
    }

    public function testGetChoicesContainsAllLabelsAndValues(): void
    {
        $choices = Timezone::getChoices();

        // Проверяем общее количество (сейчас в Enum 26 кейсов)
        $this->assertCount(26, $choices);

        // Проверяем конкретные пары
        $this->assertArrayHasKey('Лос-Анджелес', $choices);
        $this->assertEquals('America/Los_Angeles', $choices['Лос-Анджелес']);

        $this->assertArrayHasKey('Токио', $choices);
        $this->assertEquals('Asia/Tokyo', $choices['Токио']);
    }

    public function testAllCasesHaveLabels(): void
    {
        foreach (Timezone::cases() as $case) {
            // Проверка, что match покрывает все кейсы и не возвращает пустую строку
            $this->assertNotEmpty($case->label(), "Отсутствует label для временной зоны: {$case->value}");
        }
    }

    public function testValuesAreValidTimezoneIdentifiers(): void
    {
        foreach (Timezone::cases() as $case) {
            // Если часовой пояс некорректен, конструктор DateTimeZone выбросит исключение
            try {
                new \DateTimeZone($case->value);
                $this->assertTrue(true);
            } catch (\Exception $e) {
                $this->fail("Некорректный идентификатор временной зоны: {$case->value}");
            }
        }
    }
}
