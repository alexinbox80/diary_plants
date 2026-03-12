<?php

namespace App\Domain\Model\Fertilizer;

use Symfony\Component\Validator\Constraints as Assert;

class CreateFertilizerModel
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
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $manufacturer,
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly ?string $applicationRate = null,
        public readonly ?string $description = null,
        public readonly ?string $comment = null,
    ) {
    }
}
