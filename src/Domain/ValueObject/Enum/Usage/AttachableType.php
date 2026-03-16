<?php

namespace App\Domain\ValueObject\Enum\Usage;

enum AttachableType: string
{
    case FERTILIZER = 'fertilizer::class';
    case PEST = 'pest::class';
    case STIMULANT = 'stimulant::class';
    case WATERING = 'watering::class';

    public static function getClass(string $value): ?string
    {
        return match ($value) {
            self::FERTILIZER->value => \App\Domain\Entity\Fertilizer::class,
            self::PEST->value => \App\Domain\Entity\Pest::class,
            self::STIMULANT->value => \App\Domain\Entity\Stimulant::class,
            self::WATERING->value => \App\Domain\Entity\Watering::class,
            default => null,
        };
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, array_column(self::cases(), 'value'), true);
    }

    public static function getChoices(): array
    {
        return [
            'Полив' => self::WATERING->value,
            'Удобрение' => self::FERTILIZER->value,
            'Вредитель' => self::PEST->value,
            'Стимулятор' => self::STIMULANT->value,
        ];
    }

    public static function getLabel(string $value): string
    {
        return match ($value) {
            self::WATERING->value => 'Полив',
            self::FERTILIZER->value => 'Удобрение',
            self::PEST->value => 'Вредитель',
            self::STIMULANT->value => 'Стимулятор',
            default => 'Неизвестно',
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function asSelectArray(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->getLabel($case->value)] = $case->value;
        }

        return $options;
    }
}
