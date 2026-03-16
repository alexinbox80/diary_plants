<?php

namespace App\Domain\Model\User;

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
        private readonly ?string $refreshToken,
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Название группы',
            'img_tag' => 'Аватар',
            'avatar_link' => 'Ссылка на аватар',
            'email' => 'Электронная почта',
            'roles' => 'Роль пользователя',
            'is_active' => 'Пользователь активен',
            'email_confirmed' => 'Почта подтверждена',
            'phone_confirmed' => 'Телефон подтвержден',
            'time_zone' => 'Часовой пояс',
            'last_name' => 'Фамилия',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'refresh_token' => 'Токен',
            'phone' => 'Телефон',
            'email_code' => 'Код подтверждения почты',
            'phone_code' => 'Код телефона почты',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'img_tag' => (!empty($this->getAvatarLink())) ? $this->getAvatarLink() : null,
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'roles' => UserRole::getLabel(UserRole::toString($this->getRoles()) ?? ''),
            'is_active' => $this->isActive() ? 'Да' : 'Нет',
            'email_confirmed' => $this->isEmailConfirmed() ? 'Да' : 'Нет',
            'phone_confirmed' => $this->isPhoneConfirmed() ? 'Да' : 'Нет',
            'time_zone' => Timezone::from($this->getTimeZone())->label(),
            'last_name' => $this->getLastName(),
            'first_name' => $this->getFirstName(),
            'middle_name' => $this->getMiddleName(),
            'refresh_token' => $this->getRefreshToken(),
            'phone' => $this->getPhone(),
            'avatar_link' => $this->getAvatarLink(),
            'email_code' => $this->getEmailCode(),
            'phone_code' => $this->getPhoneCode(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
