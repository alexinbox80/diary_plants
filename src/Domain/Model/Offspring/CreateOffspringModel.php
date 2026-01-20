<?php

namespace App\Domain\Model\Offspring;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class CreateOffspringModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $fruitingDate = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $floweringDate = null,
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly ?int $mass = null,
        public readonly ?string $color = null,
        public readonly ?string $flavor = null,
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly ?int $quantity = null,
        public readonly ?string $comment = null
    ) {
    }
}
