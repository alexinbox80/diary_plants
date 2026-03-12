<?php

namespace App\Domain\Model\Stimulant;

use Symfony\Component\Validator\Constraints as Assert;

class CreateStimulantModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $markerId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $title,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $amount,
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $applicationRate = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $manufacturer,
        public readonly ?string $description = null,
        public readonly ?string $comment = null,
    ) {
    }
}
