<?php

namespace App\Domain\Model\Repotting;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\ValueObject\Enum\Repotting\PotMaterial;
use App\Domain\ValueObject\Enum\Repotting\RepottingType;


class UpdateRepottingModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,
        #[Assert\NotBlank]
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly DateTimeImmutable $repottedAt,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [RepottingType::class, 'values'])]
        public readonly string $type,
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [PotMaterial::class, 'values'])]
        public readonly string $potMaterial,
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Length(min: 1, max: 32, maxMessage: 'Pot size must be between 1 and 32 characters long.')]
        public readonly string $potSize,
        public readonly ?string $comment = null,
    ) {
    }
}
