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
        public ?string $filename = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public ?string $path = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public ?string $mimeType = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public ?string $alt = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:255)]
        public ?string $title = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: DateTimeImmutable::class)]
        public ?DateTimeImmutable $fileDate = null,
        public ?string $description = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        public ?int $attachableId = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:5)]
        #[Assert\Length(max:50)]
        public ?string $attachableType = null,
    ) {
    }
}
