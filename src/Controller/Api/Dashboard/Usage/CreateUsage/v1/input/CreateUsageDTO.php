<?php

namespace App\Controller\Api\Dashboard\Usage\CreateUsage\v1\input;

use OpenApi\Attributes as OA;
use App\Domain\ValueObject\Enum\Usage\AttachableType;
use Symfony\Component\Validator\Constraints as Assert;

class CreateUsageDTO
{
    public function __construct(
        #[OA\Property(example: 'watering-1907')]
        public ?string $cellId = null,

        #[OA\Property(example: 19)]
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $plantId,

        #[OA\Property(example: '2026-05-04')]
        #[Assert\NotBlank]
        #[Assert\Date]
        public string $date,

        #[OA\Property(example: 2)]
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer')]
        #[Assert\Positive]
        public int $usableId,

        #[OA\Property(example: 'watering')]
        #[Assert\NotBlank]
        #[Assert\Choice(callback: [AttachableType::class, 'getValues'])]
        public string $usableType,
    ) {
    }
}
