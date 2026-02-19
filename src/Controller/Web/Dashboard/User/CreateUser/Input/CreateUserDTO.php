<?php

namespace App\Controller\Web\Dashboard\User\CreateUser\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,

        #[Assert\NotBlank]
        #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
        public string $email,

        #[Assert\NotBlank]
        public string $password,

        #[Assert\Count(min: 1, minMessage: 'At least one role must be provided.')]
        #[Assert\All([
            new Assert\NotBlank(message: 'Role value cannot be empty.'),
            new Assert\Type(type: 'string', message: 'Each role must be a string.'),
            new Assert\Regex(
                pattern: '/^ROLE_[A-Z_]+$/',
                message: 'Each role must follow the format ROLE_XXX.'
            )
        ])]
        public array $roles,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public bool $isActive,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public bool $emailConfirmed = false,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public bool $phoneConfirmed = false,

        #[Assert\NotBlank]
        #[Assert\Timezone(message: 'This value is not a valid timezone.')]
        public string $timeZone = 'Europe/Moscow',

        #[Assert\NotBlank]
        #[Assert\Regex(
            pattern: '/^[a-zA-Zа-яА-ЯёЁ]+$/u',
            message: 'Last name should contain only letters (Latin or Cyrillic).'
        )]
        public string $lastName,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public string $firstName,

        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public ?string $middleName = null,

        public ?string $refreshToken,

        #[Assert\Regex(
            pattern: '/^\+\d{11}$/',
            message: 'The phone number must start with "+" and contain exactly 11 digits after it (e.g. +79991234567).'
        )]
        public ?string $phone = null,
        public ?string $avatarLink = null,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid integer.',
        )]
        #[Assert\Length(
            min: 5,
            max: 5,
            exactMessage: 'This value should have exactly 5 digits.'
        )]
        public ?string $emailCode = null,

        #[Assert\Type(
            type: 'integer',
            message: 'The value {{ value }} is not a valid integer.',
        )]
        #[Assert\Length(
            min: 5,
            max: 5,
            exactMessage: 'This value should have exactly 5 digits.'
        )]
        public ?string $phoneCode = null,
    ) {
    }
}
