<?php

namespace App\Domain\Model\User;

use App\Domain\Entity\User;
use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\ValueObject\Enum\UserRole;

class UserModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly string $email,
        private readonly string $password,
        private readonly array $roles,
        private readonly bool $isActive,
        private readonly bool $emailConfirmed = false,
        private readonly bool $phoneConfirmed = false,
        private readonly string $timeZone = 'Europe/Moscow',
        private readonly string $lastName,
        private readonly string $firstName,
        private readonly ?string $middleName = null,
        private readonly ?string $refreshToken = null,
        private readonly ?DateTimeImmutable $expiresAt = null,
        private readonly ?string $phone = null,
        private readonly ?string $avatarLink = null,
        private readonly ?string $emailCode = null,
        private readonly ?string $phoneCode = null,
        private readonly ?GroupModel $group = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getRefreshTokenExpiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    public function isPhoneConfirmed(): bool
    {
        return $this->phoneConfirmed;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAvatarLink(): ?string
    {
        return $this->avatarLink;
    }

    public function getEmailCode(): ?string
    {
        return $this->emailCode;
    }

    public function getPhoneCode(): ?string
    {
        return $this->phoneCode;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param User $user
     * @param GroupModel|null $groupModel
     * @return UserModel
     */
    public static function fromEntity(User $user, ?GroupModel $groupModel = null): self
    {
        return new self(
            $user->getId(),
            $user->getGroup()->getId(),
            $user->getEmail(),
            $user->getPassword(),
            $user->getRoles(),
            $user->isActive(),
            $user->isEmailConfirmed(),
            $user->isPhoneConfirmed(),
            $user->getTimeZone(),
            $user->getName()->getLast(),
            $user->getName()->getFirst(),
            $user->getName()->getMiddle(),
            $user->getRefreshToken()?->getToken(),
            $user->getRefreshToken()?->getExpiresAt(),
            $user->getPhone(),
            $user->getAvatarLink(),
            $user->getEmailCode(),
            $user->getPhoneCode(),
            $groupModel,
            $user->getCreatedAt(),
            $user->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.user.header.id',
            'group_id' => 'table.user.header.group_id',
            'group_title' => 'table.user.header.group_title',
            'img_tag' => 'table.user.header.img_tag',
            'avatar_link' => 'table.user.header.avatar_link',
            'email' => 'table.user.header.email',
            'roles' => 'table.user.header.roles',
            'is_active' => 'table.user.header.is_active',
            'email_confirmed' => 'table.user.header.email_confirmed',
            'phone_confirmed' => 'table.user.header.phone_confirmed',
            'time_zone' => 'table.user.header.time_zone',
            'last_name' => 'table.user.header.last_name',
            'first_name' => 'table.user.header.first_name',
            'middle_name' => 'table.user.header.middle_name',
            'refresh_token' => 'table.user.header.refresh_token',
            'refresh_token_expires_at' => 'table.user.header.refresh_token_expires_at',
            'phone' => 'table.user.header.phone',
            'email_code' => 'table.user.header.email_code',
            'phone_code' => 'table.user.header.phone_code',
            'created_at' => 'table.user.header.created_at',
            'updated_at' => 'table.user.header.updated_at'
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
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'img_tag' => (!empty($this->getAvatarLink())) ? $this->getAvatarLink() : null,
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'roles' => UserRole::getLabelKey(UserRole::toString($this->getRoles()) ?? ''),
            'is_active' => $this->isActive() ? 'Да' : 'Нет',
            'email_confirmed' => $this->isEmailConfirmed() ? 'Да' : 'Нет',
            'phone_confirmed' => $this->isPhoneConfirmed() ? 'Да' : 'Нет',
            'time_zone' => Timezone::from($this->getTimeZone())->labelKey(),
            'last_name' => $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'refresh_token' => $this->getRefreshToken(),
            'refresh_token_expires_at' =>  $this->getRefreshTokenExpiresAt()?->setTimezone($timezone)?->format('d.m.Y H:i:s'),
            'phone' => $this->getPhone(),
            'avatar_link' => $this->getAvatarLink(),
            'email_code' => $this->getEmailCode(),
            'phone_code' => $this->getPhoneCode(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
