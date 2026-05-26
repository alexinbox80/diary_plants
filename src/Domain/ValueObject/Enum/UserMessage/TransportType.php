<?php

namespace App\Domain\ValueObject\Enum\UserMessage;

enum TransportType: string
{
    case EMAIL = 'email'; // отправить электронное письмо пользователю / группе
    case SMS = 'sms'; // отправить сообщение пользователю / группе
    case PUSH = 'push'; // отправить пуш уведомление пользователю / группе
    case TELEGRAM = 'telegram'; // отправить уведомление через telegram пользователю / группе
    case MAX = 'max'; // отправить уведомление через max пользователю / группе
    case INTERNAL = 'internal'; // отправить уведомление на страницу администрирования авторизованному пользователю / группе

    public function getLabelKey(): string
    {
        return match($this) {
            self::EMAIL => 'user_message.transport_type.email',
            self::SMS => 'user_message.transport_type.sms',
            self::PUSH => 'user_message.transport_type.push',
            self::TELEGRAM => 'user_message.transport_type.telegram',
            self::MAX => 'user_message.transport_type.max',
            self::INTERNAL => 'user_message.transport_type.internal',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::EMAIL => '✉️',
            self::SMS => '📱',
            self::PUSH => '🔔',
            self::TELEGRAM => '✈️',
            self::MAX => '✈️',
            self::INTERNAL => '💻',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
