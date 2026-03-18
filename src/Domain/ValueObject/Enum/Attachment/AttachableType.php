<?php

namespace App\Domain\ValueObject\Enum\Attachment;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Offspring;

enum AttachableType: string
{
    case PLANT = 'plant';
    case OFFSPRING = 'offspring';

    public static function getClass(string $value): ?string
    {
        return match ($value) {
            self::PLANT->value => Plant::class,
            self::OFFSPRING->value => Offspring::class,
            default => null,
        };
    }

    public static function fromClass(string $className): self
    {
        if (str_contains($className, 'Proxies\__CG__\\')) {
            $className = str_replace('Proxies\__CG__\\', '', $className);
        }

        return match ($className) {
            Plant::class => self::PLANT,
            Offspring::class => self::OFFSPRING,
            default => self::tryFrom($className) ?? throw new \InvalidArgumentException("Unknown class: $className"),
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
