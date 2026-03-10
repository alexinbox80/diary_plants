<?php

namespace App\Domain\Model\Marker;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMarkerModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 3, maxMessage: 'Letter must be exactly one character long.')]
        public readonly string $letter,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 7, max: 7, maxMessage: 'Letter must be exactly seven character long. i.e. #ffffff')]
        public readonly string $color,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 20, maxMessage: 'Type must be exactly one character long.')]
        public readonly string $type,
        public readonly ?string $description = null,
        public readonly ?string $colorDescription = null,
    ) {
    }
}
