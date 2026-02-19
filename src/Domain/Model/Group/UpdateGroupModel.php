<?php

namespace App\Domain\Model\Group;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateGroupModel
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $isActive,

        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $title,

        public readonly ?string $description = null
    ) {
    }
}
