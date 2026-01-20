<?php

namespace App\Controller\Web\Admin\Image\EditImage\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class EditImageDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly string $filename,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly string $path,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly string $mimeType,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly string $alt,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly string $title,
        #[Assert\NotBlank]
        #[Assert\Type(type: DateTimeImmutable::class)]
        public readonly DateTimeImmutable $fileDate,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string')]
        public readonly string $attachableId,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly string $attachableType,
        public readonly ?string $description = null,
    ) {
    }
}
