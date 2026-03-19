<?php

namespace App\Domain\ValueObject\Enum\Usage;

use App\Domain\Entity\Pest;
use App\Domain\Entity\Watering;
use App\Domain\Entity\Stimulant;
use App\Domain\Entity\Fertilizer;

enum AttachableType: string
{
    case FERTILIZER = 'fertilizer';
    case PEST = 'pest';
    case STIMULANT = 'stimulant';
    case WATERING = 'watering';

    public static function getClass(string $value): ?string
    {
        return match ($value) {
            self::FERTILIZER->value => Fertilizer::class,
            self::PEST->value => Pest::class,
            self::STIMULANT->value => Stimulant::class,
            self::WATERING->value => Watering::class,
            default => null,
        };
    }

    public static function fromClass(string $className): self
    {
        if (str_contains($className, 'Proxies\__CG__\\')) {
            $className = str_replace('Proxies\__CG__\\', '', $className);
        }

        return match ($className) {
            Fertilizer::class => self::FERTILIZER,
            Stimulant::class => self::STIMULANT,
            Pest::class => self::PEST,
            Watering::class => self::WATERING,
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
