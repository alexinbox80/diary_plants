<?php

namespace App\Controller\Web\Dashboard\Marker\EditMarker\Input;

use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\Validator\Constraints as Assert;

class EditMarkerDTO
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
        #[Assert\Choice(callback: [AttachableType::class, 'getValues'])]
        public string $type,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $description = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $colorDescription = null
    ) {
    }
}
