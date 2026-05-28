<?php

namespace App\Domain\ValueObject\Enum\UserMessage;

enum MessageSenderType: string
{
    case SYSTEM = 'system';
    case USER = 'user';

    public function getLabelKey(): string
    {
        return match($this) {
            self::SYSTEM => 'user_message.sender.system',
            self::USER => 'user_message.sender.user',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
