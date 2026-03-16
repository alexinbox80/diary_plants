<?php

namespace App\Domain\ValueObject\Enum\Watering;

enum WateringMethod: string
{
    case TOP = 'top';              // Верхний (в грунт)
    case BOTTOM = 'bottom';        // Нижний (в поддон)
    case IMMERSION = 'immersion';  // Погружение (замачивание)
    case SPRAY = 'spray';          // Опрыскивание (туман)
    case WICK = 'wick';            // Фитильный полив

    public function getLabel(): string
    {
        return match($this) {
            self::TOP => 'Верхний полив',
            self::BOTTOM => 'В поддон',
            self::IMMERSION => 'Погружение',
            self::SPRAY => 'Опрыскивание',
            self::WICK => 'Фитильный',
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
            $options[$case->getLabel()] = $case->value;
        }

        return $options;
    }
}
