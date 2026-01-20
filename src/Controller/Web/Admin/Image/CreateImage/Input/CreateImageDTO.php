<?php

namespace App\Controller\Web\Admin\Image\CreateImage\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateImageDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly ?string $filename = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly ?string $path = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly ?string $mimeType = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly ?string $alt = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public readonly ?string $title = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: DateTimeImmutable::class)]
        public readonly ?DateTimeImmutable $fileDate = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        public readonly ?int $attachableId = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public readonly ?string $attachableType = null,
        public readonly ?string $description = null,
    ) {
    }
}
