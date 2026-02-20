<?php

namespace App\Domain\ValueObject\Enum;

enum Timezone: string
{
    case Utc = 'UTC';
    case Kaliningrad = 'Europe/Kaliningrad';
    case Moscow = 'Europe/Moscow';
    case Kiev = 'Europe/Kiev';
    case Minsk = 'Europe/Minsk';

    // Россия (Восток)
    case Yekaterinburg = 'Asia/Yekaterinburg';
    case Omsk = 'Asia/Omsk';
    case Novosibirsk = 'Asia/Novosibirsk';
    case Krasnoyarsk = 'Asia/Krasnoyarsk';
    case Irkutsk = 'Asia/Irkutsk';
    case Yakutsk = 'Asia/Yakutsk';
    case Vladivostok = 'Asia/Vladivostok';
    case Magadan = 'Asia/Magadan';
    case Kamchatka = 'Asia/Kamchatka';

    // Европа
    case London = 'Europe/London';
    case Paris = 'Europe/Paris';
    case Berlin = 'Europe/Berlin';

    // Азия и Океания
    case Dubai = 'Asia/Dubai';
    case Shanghai = 'Asia/Shanghai';
    case Tokyo = 'Asia/Tokyo';
    case Sydney = 'Australia/Sydney';

    // Америка
    case NewYork = 'America/New_York';
    case Chicago = 'America/Chicago';
    case Denver = 'America/Denver';
    case LosAngeles = 'America/Los_Angeles';
    case SaoPaulo = 'America/Sao_Paulo';

    /**
     * Возвращает человекочитаемое название города
     */
    public function label(): string
    {
        return match($this) {
            self::Utc => 'UTC',
            self::Kaliningrad => 'Калининград',
            self::Moscow => 'Москва',
            self::Kiev => 'Киев',
            self::Minsk => 'Минск',
            self::Yekaterinburg => 'Екатеринбург',
            self::Omsk => 'Омск',
            self::Novosibirsk => 'Новосибирск',
            self::Krasnoyarsk => 'Красноярск',
            self::Irkutsk => 'Иркутск',
            self::Yakutsk => 'Якутск',
            self::Vladivostok => 'Владивосток',
            self::Magadan => 'Магадан',
            self::Kamchatka => 'Камчатка',
            self::NewYork => 'Нью-Йорк',
            self::Chicago => 'Чикаго',
            self::Denver => 'Денвер',
            self::LosAngeles => 'Лос-Анджелес',
            self::SaoPaulo => 'Сан-Пауло',
            self::Sydney => 'Сидней',
            self::Tokyo => 'Токио',
            self::Shanghai => 'Шанхай',
            self::Dubai => 'Дубай',
            self::London => 'Лондон',
            self::Paris => 'Париж',
            self::Berlin => 'Берлин',
        };
    }

    /**
     * Полезно для выпадающих списков (ChoiceType в Symfony)
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            $choices[$case->label()] = $case->value;
        }
        return $choices;
    }
}
