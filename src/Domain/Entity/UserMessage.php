<?php // сущность для хранения сообщений пользователю

namespace App\Domain\Entity;

use LogicException;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\ValueObject\UserMessage\MessageSender;
use App\Domain\ValueObject\UserMessage\MessageContent;
use App\Domain\ValueObject\UserMessage\MessageRecipient;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\ValueObject\Enum\UserMessage\TransportType;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use App\Domain\ValueObject\Enum\UserMessage\NotificationStatus;

#[ORM\Table(name: '`user_message`')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'user_message__user_id__ind', columns: ['user_id'])]
#[ORM\Index(name: 'user_message__group_id__ind', columns: ['group_id'])]
#[ORM\Index(name: 'user_message__sender_id__ind', columns: ['sender_id'])]
#[ORM\Index(name: 'user_message__status__ind', columns: ['status'])]
final class UserMessage implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    #[ORM\Column(name: 'id', type: 'bigint', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    // VO Получателя (объединяет user_id и group_id)
    #[ORM\Embedded(class: MessageRecipient::class, columnPrefix: false)]
    private MessageRecipient $recipient;

    // VO Отправителя (объединяет sender_id и sender_type)
    #[ORM\Embedded(class: MessageSender::class, columnPrefix: false)]
    private MessageSender $sender;

    // VO Контента (объединяет translation_key и parameters)
    #[ORM\Embedded(class: MessageContent::class, columnPrefix: false)]
    private MessageContent $content;

    // дефолтная локаль для групповой рассылки (если в групповой рассылке локаль null берем у нужного пользователя)
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
        MessageContent $content,
        TransportType $transportType,
        string $locale = 'ru',
        ?MessageSender $sender = null
    ): self {
        $message = new self();
        $message->recipient = MessageRecipient::fromUser($userId);
        $message->populateCommonFields($content, $transportType, $locale, $sender);

        return $message;
    }

    // Фабрика для рассылки на группу
    public static function createToGroup(
        int $groupId,
        MessageContent $content,
        TransportType $transportType,
        ?string $locale = null,
        ?MessageSender $sender = null
    ): self {
        $message = new self();
        $message->recipient = MessageRecipient::fromGroup($groupId);
        $message->populateCommonFields($content, $transportType, $locale, $sender);

        return $message;
    }

    private function populateCommonFields(
        MessageContent $content,
        TransportType $transportType,
        ?string $locale = null,
        ?MessageSender $sender = null
    ): void {
        $this->content = $content;
        $this->transportType = $transportType;
        $this->locale = $locale;
        $this->sender = $sender ?? MessageSender::asSystem();
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));

        return $this->id;
    }

    public function getRecipient(): MessageRecipient
    {
        return $this->recipient;
    }

    public function getSender(): MessageSender
    {
        return $this->sender;
    }

    public function getContent(): MessageContent
    {
        return $this->content;
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

    public function markAsRead(): self
    {
        WebmozartAssert::false($this->status === NotificationStatus::FAILED, 'Cannot mark a failed message as read.');

        if ($this->readAt === null) {
            $this->readAt = new DateTimeImmutable();
            // Полезно автоматически менять статус на READ, если логика это подразумевает
            $this->status = NotificationStatus::SENT;
        }

        return $this;
    }

    public function changeStatus(NotificationStatus $status): self
    {
        // Защита инварианта: нельзя перевести уже прочитанное сообщение обратно в PENDING
        if ($this->isRead() && $status === NotificationStatus::PENDING) {
            throw new LogicException('Cannot revert already read message to pending status.');
        }

        $this->status = $status;

        return $this;
    }

    public function markAsFailed(string $errorMessage): self
    {
        WebmozartAssert::notEmpty($errorMessage, 'Error message cannot be empty.');

        $this->status = NotificationStatus::FAILED;
        $this->errorMessage = $errorMessage;

        return $this;
    }
}
