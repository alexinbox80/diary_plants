<?php

namespace App\Domain\ValueObject\Enum\Repotting;

enum RepottingType: string
{
    // Основные типы
    case POTTING_UP = 'potting_up';       // Перевалка
    case REPOT_FULL = 'repot_full';       // Полная пересадка
    case DIVISION = 'division';           // Деление куста
    case TOP_DRESSING = 'top_dressing';   // Замена верхнего слоя

    // Специфические типы
    case PRICKING_OUT = 'pricking_out';   // Пикировка
    case PLANTING = 'planting';           // Посадка саженца
    case ROOT_PRUNING = 'root_pruning';   // Обрезка корней
    case EMERGENCY = 'emergency';         // Реанимационная пересадка

    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->getLabelKey()] = $case->value;
        }
        return $choices;
    }

    /**
     * Возвращает человекопонятное название на русском
     */
    public function getLabelKey(): string
    {
        return match($this) {
            self::POTTING_UP    => 'repot_type.potting_up',
            self::REPOT_FULL    => 'repot_type.repot_full',
            self::DIVISION      => 'repot_type.division',
            self::TOP_DRESSING  => 'repot_type.top_dressing',
            self::PRICKING_OUT  => 'repot_type.pricking_out',
            self::PLANTING      => 'repot_type.planting',
            self::ROOT_PRUNING  => 'repot_type.root_pruning',
            self::EMERGENCY     => 'repot_type.emergency',
        };
    }

    /**
     * Описание метода (можно использовать для подсказок в интерфейсе)
     */
    public function getDescription(): string
    {
        return match($this) {
            self::POTTING_UP    => 'В более крупную емкость без разрушения земляного кома. Самый щадящий метод.',
            self::REPOT_FULL    => 'С полной или частичной заменой старого грунта и очисткой корней.',
            self::DIVISION      => 'Размножение или омоложение путем разделения корневища на части.',
            self::TOP_DRESSING  => 'Меняют только верхние 2-5 см грунта (для крупных растений).',
            self::PRICKING_OUT  => 'Первая пересадка сеянцев из общей емкости в индивидуальные горшки.',
            self::PLANTING      => 'Переезд из горшка в открытый грунт или наоборот.',
            self::ROOT_PRUNING  => 'Возврат в тот же горшок с подрезкой корней (для бонсай).',
            self::EMERGENCY     => 'Вынужденная пересадка из-за гнили или вредителей.',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
