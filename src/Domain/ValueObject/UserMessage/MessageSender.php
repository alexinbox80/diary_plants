<?php // группирует поля идентификатор отправителя и тип отправителя

namespace App\Domain\ValueObject\UserMessage;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\Enum\UserMessage\MessageSenderType;

#[ORM\Embeddable]
final class MessageSender
{
    // Идентификатор отправителя-пользователя (NULL для системы)
    #[ORM\Column(name: 'sender_id', type: 'integer', nullable: true)]
    private ?int $senderId = null;

    // Тип отправителя: 'system' или 'user' или 'bot' или еще что-то ...
    #[ORM\Column(name: 'sender_type', type: 'string', length: 16, enumType: MessageSenderType::class)]
    private MessageSenderType $type;

    private function __construct(?int $senderId, MessageSenderType $type)
    {
        $this->senderId = $senderId;
        $this->type = $type;
    }

    public static function asSystem(): self
    {
        return new self(null, MessageSenderType::SYSTEM);
    }

    public static function asUser(int $senderId): self
    {
        return new self($senderId, MessageSenderType::USER);
    }

    public static function asBot(int $botId): self
    {
        return new self($botId, MessageSenderType::BOT);
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function getType(): MessageSenderType
    {
        return $this->type;
    }
}
