<?php

namespace App\Domain\Model\Pest;

use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

class UpdatePestModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $plantId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $title,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $manufacturer,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $quantity,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'DateTime::class')]
        public readonly DateTime $useDate,
        public readonly ?string $description = null,
        public readonly ?string $comment = null,
    ) {
    }
}
