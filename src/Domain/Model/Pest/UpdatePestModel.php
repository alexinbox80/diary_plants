<?php

namespace App\Domain\Model\Pest;

use Symfony\Component\Validator\Constraints as Assert;

class UpdatePestModel
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
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $manufacturer = null,
        public readonly ?string $description = null,
        public readonly ?string $comment = null,
    ) {
    }
}
