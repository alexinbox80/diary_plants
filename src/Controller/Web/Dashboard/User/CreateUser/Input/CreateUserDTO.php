<?php

namespace App\Controller\Web\Dashboard\User\CreateUser\Input;

use App\Domain\ValueObject\Enum\UserRole;
use App\Domain\ValueObject\Enum\ImageMimeType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateUserDTO
{
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->avatarFile instanceof UploadedFile) {
            $mimeType = $this->avatarFile->getMimeType();
            $fileSize = $this->avatarFile->getSize();

            if (!in_array($mimeType, ImageMimeType::getValues())) {
                $context->buildViolation('Недопустимый MIME-тип изображения: {{ type }}')
                    ->atPath('imageFile')
                    ->setParameter('{{ type }}', $mimeType)
                    ->addViolation();
            }

            if ($fileSize > 500_000) {
                $context->buildViolation('Размер файла слишком большой — {{ size }} байт. Максимум: 0.5 МБ.')
                    ->atPath('imageFile')
                    ->setParameter('{{ size }}', $fileSize)
                    ->addViolation();
            }

            // Валидация роли через enum UserRole
            if (!UserRole::isValid($this->roles)) {
                $context->buildViolation('Роль должна быть одной из: ' . UserRole::AllRolesToString())
                    ->atPath('roles')
                    ->addViolation();
            }
        }
    }

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,

        #[Assert\NotBlank]
        #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
        #[Assert\Length(max: 64, maxMessage: 'Email cannot be longer than 64 characters.')]
        public string $email,

        #[Assert\NotBlank]
        public string $password,

        public string $roles,

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
        #[Assert\Regex(
            pattern: '/^[a-zA-Zа-яА-ЯёЁ]+$/u',
            message: 'First name should contain only letters (Latin or Cyrillic).'
        )]
        public string $firstName,

        #[Assert\Regex(
            pattern: '/^[a-zA-Zа-яА-ЯёЁ]+$/u',
            message: 'Middle name should contain only letters (Latin or Cyrillic).'
        )]
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

        //#[Assert\NotNull(message: 'Пожалуйста, выберите изображение')]
        public ?UploadedFile $avatarFile = null,
    ) {
    }
}
