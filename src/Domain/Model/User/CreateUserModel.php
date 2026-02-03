<?php

namespace App\Domain\Model\User;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,

        #[Assert\NotBlank]
        #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
        public readonly string $email,

        #[Assert\NotBlank]
        public readonly string $password,

        #[Assert\Count(min: 1, minMessage: 'At least one role must be provided.')]
        #[Assert\All([
            new Assert\NotBlank(message: 'Role value cannot be empty.'),
            new Assert\Type(type: 'string', message: 'Each role must be a string.'),
            new Assert\Regex(
                pattern: '/^ROLE_[A-Z_]+$/',
                message: 'Each role must follow the format ROLE_XXX.'
            )
        ])]
        public readonly array $roles,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $isActive,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $emailConfirmed = false,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $phoneConfirmed = false,

        #[Assert\NotBlank]
        #[Assert\Timezone(message: 'This value is not a valid timezone.')]
        public readonly string $timeZone = 'Europe/Moscow',

        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^[a-zA-Zа-яА-ЯёЁ]+$/u',
            message: 'Last name should contain only letters (Latin or Cyrillic).'
        )]
        public readonly string $lastName,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $firstName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $middleName = null,

        public readonly ?string $refreshToken,

        #[Assert\Regex(
            pattern: '/^\+\d{11}$/',
            message: 'The phone number must start with "+" and contain exactly 11 digits after it (e.g. +79991234567).'
        )]
        public readonly ?string $phone = null,
        public readonly ?string $avatarLink = null,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid integer.',
        )]
        #[Assert\Length(
            min: 5,
            max: 5,
            exactMessage: 'This value should have exactly 5 digits.'
        )]
        public readonly ?string $emailCode = null,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid integer.',
        )]
        #[Assert\Length(
            min: 5,
            max: 5,
            exactMessage: 'This value should have exactly 5 digits.'
        )]
        public readonly ?string $phoneCode = null,
    ) {}
}
