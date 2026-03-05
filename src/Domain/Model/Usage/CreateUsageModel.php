<?php

namespace App\Domain\Model\Usage;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class CreateUsageModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        public readonly DateTimeImmutable $useDate,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,
        public readonly ?string $comment = null,
        #[Assert\Type(type: ['null', 'int'], message: 'The value must be a boolean.')]
        public readonly ?int $usableId = null,
        public readonly ?string $usableType = null
    ) {
    }
}
