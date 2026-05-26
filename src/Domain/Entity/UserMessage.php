<?php

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\ValueObject\Enum\UserMessage\TransportType;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\ValueObject\Enum\UserMessage\NotificationStatus;

#[ORM\Table(name: '`user_message`')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'user_message__user_id__ind', columns: ['user_id'])]
#[ORM\Index(name: 'user_message__group_id__ind', columns: ['group_id'])]
#[ORM\Index(name: 'user_message__status__ind', columns: ['status'])]
final class UserMessage implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    // NULL, если сообщение отправлено конкретной ГРУППЕ
    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true)]
    private ?int $userId = null;

    // NULL, если сообщение отправлено конкретному ПОЛЬЗОВАТЕЛЮ
    #[ORM\Column(name: 'group_id', type: 'integer', nullable: true)]
    private ?int $groupId = null;

    // ключ шаблона сообщения из message.<lang>.yaml
    #[ORM\Column(name: 'translation_key', type: 'string', length: 255)]
    private string $translationKey;

    // для хранения переменных данных, которые подставляются в шаблон при отправке
    #[ORM\Column(name: 'parameter', type: 'json')]
    private array $parameters = [];

    // дефолтная локаль для групповой рассылки
    #[ORM\Column(name: 'locale', type: 'string', length: 7, nullable: true)]
    private ?string $locale = null;

    // тип транспорта
    #[ORM\Column(name: 'transport_type', type: Types::STRING, length: 16, enumType: TransportType::class)]
    private TransportType $transportType = TransportType::INTERNAL;

    // статус сообщения
    #[ORM\Column(name: 'status', type: Types::STRING, length: 16, enumType: NotificationStatus::class)]
    private NotificationStatus $status = NotificationStatus::PENDING;

    // дата и время прочтения сообщения, если null то сообщение не прочитано
    #[ORM\Column(name: 'read_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $readAt = null;

    // техническая ошибка если статус сообщения failed
    #[ORM\Column(name: 'error_message', type: 'text', nullable: true)]
    private ?string $errorMessage = null;

    // Конструктор разбит на именованные фабричные методы для удобства
    private function __construct() {}

    // Фабрика для личного сообщения
    public static function createToUser(
        int $userId,
        string $translationKey,
        array $parameters,
        string $locale,
        TransportType $transportType
    ): self {
        $message = new self();
        $message->userId = $userId;
        $message->translationKey = $translationKey;
        $message->parameters = $parameters;
        $message->locale = $locale;
        $message->transportType = $transportType;

        return $message;
    }

    // Фабрика для рассылки на группу
    public static function createToGroup(
        int $groupId,
        string $translationKey,
        array $parameters,
        TransportType $transportType,
        ?string $defaultLocale = 'ru'
    ): self {
        $message = new self();
        $message->groupId = $groupId;
        $message->translationKey = $translationKey;
        $message->parameters = $parameters;
        $message->locale = $defaultLocale;
        $message->transportType = $transportType;

        return $message;
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getTransportType(): TransportType
    {
        return $this->transportType;
    }

    public function getStatus(): NotificationStatus
    {
        return $this->status;
    }

    public function getReadAt(): ?DateTimeImmutable
    {
        return $this->readAt;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isRead(): bool
    {
        return $this->readAt !== null;
    }

    /**
     * Пометить сообщение как прочитанное авторизованным пользователем
     */
    public function markAsRead(): self
    {
        if ($this->readAt === null) {
            $this->readAt = new DateTimeImmutable();
        }

        return $this;
    }

    /**
     * Изменить статус (например, перевести в PROCESSING или SENT воркером)
     */
    public function changeStatus(NotificationStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Зафиксировать сбой отправки транспорта
     */
    public function markAsFailed(string $errorMessage): self
    {
        $this->status = NotificationStatus::FAILED;
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
