<?php // enum для статуса сообщений пользователю

namespace App\Domain\ValueObject\Enum\UserMessage;

enum NotificationStatus: string
{
    case PENDING= 'pending'; // сообщение висит на отправку в очереди
    case PROCESSING = 'processing'; // сообщение в процессе отправки в обработке
    case SENT = 'sent'; // сообщение отправлено
    case FAILED = 'failed';  // ошибка отправки сообщения

    /**
     * Возвращает понятное описание статуса на русском языке (для админки)
     */
    public function getLabelKey(): string
    {
        return match($this) {
            self::PENDING => 'user_message.notification_status.pending',
            self::PROCESSING => 'user_message.notification_status.processing',
            self::SENT => 'user_message.notification_status.sent',
            self::FAILED => 'user_message.notification_status.failed',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
