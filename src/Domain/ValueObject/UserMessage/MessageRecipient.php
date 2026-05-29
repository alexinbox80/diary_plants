<?php // группирует поля идентификатор пользователя и идентификатор группы не могут одновременно иметь одинаковые значения

namespace App\Domain\ValueObject\UserMessage;

use Webmozart\Assert\Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class MessageRecipient
{
    // NULL, если сообщение отправлено конкретной ГРУППЕ
    #[ORM\Column(name: 'user_id', type: 'integer', nullable: true)]
    private ?int $userId = null;

    // NULL, если сообщение отправлено конкретному ПОЛЬЗОВАТЕЛЮ
    #[ORM\Column(name: 'group_id', type: 'integer', nullable: true)]
    private ?int $groupId = null;

    private function __construct() {}

    public static function fromUser(int $userId): self
    {
        Assert::greaterThan($userId, 0, 'User ID must be greater than 0.');

        $recipient = new self();
        $recipient->userId = $userId;
        return $recipient;
    }

    public static function fromGroup(int $groupId): self
    {
        Assert::greaterThan($groupId, 0, 'Group ID must be greater than 0.');

        $recipient = new self();
        $recipient->groupId = $groupId;
        return $recipient;
    }

    // Валидация инварианта при реальном обращении (опционально, для подстраховки)
    public function validate(): void
    {
        Assert::true(
            ($this->userId !== null && $this->groupId === null) || ($this->userId === null && $this->groupId !== null),
            'Message recipient state is invalid.'
        );
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getGroupId(): ?int
    {
        return $this->groupId;
    }
}
