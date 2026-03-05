<?php

namespace App\Domain\ValueObject\Enum\Usage;

enum AttachableType: string
{
    case FERTILIZER = 'fertilizer::class';
    case PEST = 'pest::class';
    case STIMULANT = 'stimulant::class';

    public static function getClass(string $value): ?string
    {
        return match ($value) {
            self::FERTILIZER->value => \App\Domain\Entity\Fertilizer::class,
            self::PEST->value => \App\Domain\Entity\Pest::class,
            self::STIMULANT->value => \App\Domain\Entity\Stimulant::class,
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
            'Удобрение' => self::FERTILIZER->value,
            'Вредитель' => self::PEST->value,
            'Стимулятор' => self::STIMULANT->value,
        ];
    }

    public static function getLabel(string $value): string
    {
        return match ($value) {
            self::FERTILIZER->value => 'Удобрение',
            self::PEST->value => 'Вредитель',
            self::STIMULANT->value => 'Стимулятор',
            default => 'Неизвестно',
        };
    }
}
