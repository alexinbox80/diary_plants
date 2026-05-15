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
            $choices[$case->getLabelKey()] = $case->value;
        }
        return $choices;
    }

    /**
     * Возвращает человекопонятное название материала на русском
     */
    public function getLabelKey(): string
    {
        return match($this) {
            self::PLASTIC    => 'pot_material.plastic',
            self::CERAMIC    => 'pot_material.ceramic',
            self::TERRACOTTA => 'pot_material.terracotta',
            self::TEXTILE    => 'pot_material.textile',
            self::PEAT       => 'pot_material.peat',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
