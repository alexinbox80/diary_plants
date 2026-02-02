<?php

namespace App\Domain\Model\User;

use DateTimeImmutable;

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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
