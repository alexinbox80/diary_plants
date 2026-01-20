<?php

namespace App\Domain\Model\Task;

use Symfony\Component\Validator\Constraints as Assert;
use DateTimeImmutable;

class CreateTaskModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $statusId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,
        #[Assert\NotBlank]
        public readonly DateTimeImmutable $date,
        public readonly ?string $description = null
    ) {
    }
}
