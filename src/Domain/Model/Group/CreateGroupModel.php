<?php

namespace App\Domain\Model\Group;

use Symfony\Component\Validator\Constraints as Assert;

class CreateGroupModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public readonly string $title,

        #[Assert\NotNull]
        #[Assert\Type(type: 'bool', message: 'The value {{ value }} is not a valid boolean.')]
        public readonly bool $isActive,

        public readonly ?string $description = null
    ) {
    }
}
