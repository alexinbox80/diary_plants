<?php

namespace App\Domain\Model\Status;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateStatusModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 1, maxMessage: 'Letter must be exactly one character long.')]
        public readonly string $letter,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 7, max: 7, maxMessage: 'Letter must be exactly seven character long. i.e. #ffffff')]
        public readonly string $color,
        public readonly ?string $description = null,
        public readonly ?string $colorDescription = null,
    ) {
    }
}
