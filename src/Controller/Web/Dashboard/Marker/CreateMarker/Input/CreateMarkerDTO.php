<?php

namespace App\Controller\Web\Dashboard\Marker\CreateMarker\Input;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMarkerDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,
        #[Assert\NotBlank]
        #[Assert\Length(min:1)]
        #[Assert\Length(max:3)]
        public string $letter,
        #[Assert\NotBlank]
        #[Assert\Length(min:7)]
        #[Assert\Length(max:7)]
        public string $color,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 20, maxMessage: 'Type must be exactly one character long.')]
        public readonly string $type,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public readonly ?string $description = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public readonly ?string $colorDescription = null
    ) {
    }
}
