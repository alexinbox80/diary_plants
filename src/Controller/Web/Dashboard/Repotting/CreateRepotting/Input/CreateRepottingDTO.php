<?php

namespace App\Controller\Web\Dashboard\Repotting\CreateRepotting\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;

class CreateRepottingDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $plantId,

        #[Assert\NotBlank]
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public DateTimeImmutable $repottedAt,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [RepottingType::class, 'values'])]
        public string $type,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [PotMaterial::class, 'values'])]
        public string $potMaterial,

        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 32, maxMessage: 'Pot size must be between 1 and 32 characters long.')]
        public string $potSize,

        public ?string $comment = null
    ) {
    }
}
