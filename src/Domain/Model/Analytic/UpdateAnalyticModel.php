<?php

namespace App\Domain\Model\Analytic;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateAnalyticModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,

        public readonly int $count = 0,
        public readonly float $averageDays = 0.0,
    ) {
    }
}
