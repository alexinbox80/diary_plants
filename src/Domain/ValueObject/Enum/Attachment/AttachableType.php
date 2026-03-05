<?php

namespace App\Domain\ValueObject\Enum\Attachment;

enum AttachableType: string
{
    case PLANT = 'plant::class';
    case OFFSPRING = 'offspring::class';

    public static function getClass(string $value): ?string
    {
        return match ($value) {
            self::PLANT->value => \App\Domain\Entity\Plant::class,
            self::OFFSPRING->value => \App\Domain\Entity\Offspring::class,
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
            'Растение' => self::PLANT->value,
            'Плод' => self::OFFSPRING->value,
        ];
    }

    public static function getLabel(string $value): string
    {
        return match ($value) {
            self::PLANT->value => 'Растение',
            self::OFFSPRING->value => 'Плод',
            default => 'Неизвестно',
        };
    }
}
