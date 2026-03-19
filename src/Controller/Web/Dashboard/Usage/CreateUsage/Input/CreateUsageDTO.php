<?php

namespace App\Controller\Web\Dashboard\Usage\CreateUsage\Input;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUsageDTO
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
        public DateTimeImmutable $useDate,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        public int $usableId,

        #[Assert\NotBlank]
        #[Assert\Length(min:5, max:50)]
        public string $usableType,

        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null
    ) {
    }
}
