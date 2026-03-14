<?php

namespace App\Controller\Web\Dashboard\Watering\CreateWatering\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateWateringDTO
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
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public readonly ?string $description = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public readonly ?string $comment = null,
    ) {
    }
}
