<?php

namespace App\Domain\Model\Watering;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateWateringModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $markerId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $amount,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $wateringType,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $wateringMethod,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $wateredAt = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $temperature,
        public readonly ?string $description = null,
        public readonly ?string $comment = null,
    ) {
    }
}
