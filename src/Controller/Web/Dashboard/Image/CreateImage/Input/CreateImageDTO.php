<?php

namespace App\Controller\Web\Dashboard\Image\CreateImage\Input;

use DateTimeImmutable;
use App\Domain\ValueObject\Enum\ImageMimeType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Domain\ValueObject\Enum\Attachment\AttachableType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CreateImageDTO
{

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->imageFile instanceof UploadedFile) {
            $mimeType = $this->imageFile->getMimeType();
            $fileSize = $this->imageFile->getSize();

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

            if (!in_array($this->attachableType, array_column(AttachableType::cases(), 'value'))) {
                $context->buildViolation('Invalid attachable type')->atPath('attachableType')->addViolation();
            }
        }
    }

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,

        #[Assert\Type('boolean')]
        public bool $isShown,

        public string $filename,

        public string $path,

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
