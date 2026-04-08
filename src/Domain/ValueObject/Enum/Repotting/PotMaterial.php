<?php

namespace App\Domain\ValueObject\Enum\Repotting;

enum PotMaterial: string
{
    case PLASTIC = 'plastic';   // Пластик
    case CERAMIC = 'ceramic';   // Керамика
    case TERRACOTTA = 'terra';  // Терракота (глина)
    case TEXTILE = 'textile';   // Текстильный мешок
    case PEAT = 'peat';         // Торфяной стаканчик

    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }
        return $choices;
    }

    /**
     * Возвращает человекопонятное название материала на русском
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PLASTIC    => 'Пластик',
            self::CERAMIC    => 'Керамика',
            self::TERRACOTTA => 'Терракота (глина)',
            self::TEXTILE    => 'Текстильный мешок',
            self::PEAT       => 'Торфяной стаканчик',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
