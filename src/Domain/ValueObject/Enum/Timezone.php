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

    public function labelKey(): string
    {
        return match($this) {
            self::Utc => 'timezone.utc',
            self::Kaliningrad => 'timezone.kaliningrad',
            self::Moscow => 'timezone.moscow',
            self::Kiev => 'timezone.kiev',
            self::Minsk => 'timezone.minsk',
            self::Yekaterinburg => 'timezone.yekaterinburg',
            self::Omsk => 'timezone.omsk',
            self::Novosibirsk => 'timezone.novosibirsk',
            self::Krasnoyarsk => 'timezone.krasnoyarsk',
            self::Irkutsk => 'timezone.irkutsk',
            self::Yakutsk => 'timezone.yakutsk',
            self::Vladivostok => 'timezone.vladivostok',
            self::Magadan => 'timezone.magadan',
            self::Kamchatka => 'timezone.kamchatka',
            self::London => 'timezone.london',
            self::Paris => 'timezone.paris',
            self::Berlin => 'timezone.berlin',
            self::Dubai => 'timezone.dubai',
            self::Shanghai => 'timezone.shanghai',
            self::Tokyo => 'timezone.tokyo',
            self::Sydney => 'timezone.sydney',
            self::NewYork => 'timezone.new_york',
            self::Chicago => 'timezone.chicago',
            self::Denver => 'timezone.denver',
            self::LosAngeles => 'timezone.los_angeles',
            self::SaoPaulo => 'timezone.sao_paulo',
        };
    }

    /**
     * Полезно для выпадающих списков (ChoiceType в Symfony)
     */
    public static function getChoices(): array
    {
        $choices = [];
        foreach (self::cases() as $case) {
            //$choices[$case->label()] = $case->value;
            $choices[$case->labelKey()] = $case->value;
        }
        return $choices;
    }
}
