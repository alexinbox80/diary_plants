<?php

namespace App\Domain\Model\UserMessage;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\UserMessage;
use App\Domain\ValueObject\Enum\Timezone;

final class UserMessageModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $senderType,
        private readonly string $translationKey,
        private readonly array $parameters,
        private readonly string $transportType,
        private readonly string $status,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
        private readonly ?int $userId = null,
        private readonly ?int $groupId = null,
        private readonly ?int $senderId = null,
        private readonly ?string $locale = null,
        private readonly ?DateTimeImmutable $readAt = null,
        private readonly ?string $errorMessage = null
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSenderType(): string
    {
        return $this->senderType;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getTransportType(): string
    {
        return $this->transportType;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function getSenderId(): ?int
    {
        return $this->senderId;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @param UserMessage $userMessage
     * @return UserMessageModel
     */
    public static function fromEntity(UserMessage $userMessage): self
    {
        return new self(
            $userMessage->getId(),
            $userMessage->getSender()->getType()->value,
            $userMessage->getContent()->getTranslationKey(),
            $userMessage->getContent()->getParameters(),
            $userMessage->getTransportType()->value,
            $userMessage->getStatus()->value,
            $userMessage->getCreatedAt(),
            $userMessage->getUpdatedAt(),
            $userMessage->getRecipient()->getUserId(),
            $userMessage->getRecipient()->getGroupId(),
            $userMessage->getSender()->getSenderId(),
            $userMessage->getLocale(),
            $userMessage->getReadAt(),
            $userMessage->getErrorMessage()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.user_message.header.id',
            'user_id' => 'table.user_message.header.user_id',
            'group_id' => 'table.user_message.header.group_id',
            'sender_id' => 'table.user_message.header.sender_id',
            'sender_type' => 'table.user_message.header.sender_type',
            'locale' => 'table.user_message.header.locale',
            'read_at' => 'table.user_message.header.read_at',
            'error_message' => 'table.user_message.header.error_message',
            'translation_key' => 'table.user_message.header.translation_key',
            'parameters' => 'table.user_message.header.parameters',
            'transport_type' => 'table.user_message.header.transport_type',
            'status' => 'table.user_message.header.status',
            'created_at' => 'table.user_message.header.created_at',
            'updated_at' => 'table.user_message.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'group_id' => $this->getGroupId(),
            'sender_id' => $this->getSenderId(),
            'sender_type' => $this->getSenderType(),
            'locale' => $this->getLocale(),
            'read_at' => $this->getReadAt()?->setTimezone($timezone)?->format('d.m.Y H:i:s'),
            'error_message' => $this->getErrorMessage(),
            'translation_key' => $this->getTranslationKey(),
            'parameters' => $this->getParameters(),
            'transport_type' => $this->getTransportType(),
            'status' => $this->getStatus(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
