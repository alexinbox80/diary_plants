<?php

namespace App\Controller\Api\Dashboard\Usage\CreateUsage\v1\input;

use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUsageDTO
{
    public function __construct(
        public ?string $cellId = null,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $plantId,

        #[Assert\NotBlank]
        #[Assert\Date]
        public string $date,


        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $usableId,

        #[Assert\NotBlank]
        #[Assert\Choice(callback: [AttachableType::class, 'getValues'])]
        public string $usableType,
    ) {
    }
}
