<?php

namespace App\Controller\Web\Dashboard\Image\EditImage\Input;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditImageDTO
{
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->imageFile instanceof UploadedFile) {
            $mimeType = $this->imageFile->getMimeType();
            $fileSize = $this->imageFile->getSize();

            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp'
            ];

            if (!in_array($mimeType, $allowedMimeTypes)) {
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

            // Валидация attachableType
            $allowedTypes = [
                'plant::class',
                'offspring::class',
            ];

            if (!in_array($this->attachableType, $allowedTypes)) {
                $context->buildViolation('Недопустимый тип сущности: {{ type }}. Допустимые значения: plant::class, offspring::class')
                    ->atPath('attachableType')
                    ->setParameter('{{ type }}', $this->attachableType)
                    ->addViolation();
            }
        }
    }

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:50)]
        public string $filename,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:255)]
        public string $path,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:50)]
        public string $mimeType,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:255)]
        public string $alt,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:255)]
        public string $title,

        #[Assert\NotBlank]
        #[Assert\Type(type: DateTimeImmutable::class)]
        public DateTimeImmutable $fileDate,
        public ?string $description = null,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        public int $attachableId,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:50)]
        public string $attachableType,

        #[Assert\NotNull(message: 'Пожалуйста, выберите изображение')]
        public ?UploadedFile $imageFile = null,
    ) {
    }
}
