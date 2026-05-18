<?php

namespace App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateOffspringDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $plantId,

        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $fruitingDate = null,

        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $floweringDate = null,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        #[Assert\Positive]
        public ?int $mass = null,

        #[Assert\Length(min:2)]
        #[Assert\Length(max:64)]
        public ?string $color = null,

        #[Assert\Length(min:2)]
        #[Assert\Length(max:64)]
        public ?string $flavor = null,

        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        #[Assert\Positive]
        public ?int $quantity = null,

        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null
    ) {
    }
}
