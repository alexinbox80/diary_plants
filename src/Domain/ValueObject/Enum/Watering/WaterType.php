<?php

namespace App\Domain\ValueObject\Enum\Watering;

enum WaterType: string
{
    case SETTLED = 'settled';      // Отстоянная
    case FILTERED = 'filtered';    // Фильтрованная
    case TAP = 'tap';              // Водопроводная
    case DISTILLED = 'distilled';  // Дистиллированная
    case RAIN = 'rain';            // Дождевая
    case OSMOSIS = 'osmosis';      // Обратный осмос

    public function getLabel(): string
    {
        return match($this) {
            self::SETTLED => 'Отстоянная',
            self::FILTERED => 'Фильтрованная',
            self::TAP => 'Водопроводная',
            self::DISTILLED => 'Дистиллированная',
            self::RAIN => 'Дождевая',
            self::OSMOSIS => 'Осмос',
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
