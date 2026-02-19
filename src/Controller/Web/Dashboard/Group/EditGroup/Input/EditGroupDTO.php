<?php

namespace App\Controller\Web\Dashboard\Group\EditGroup\Input;

use Symfony\Component\Validator\Constraints as Assert;

class EditGroupDTO
{
    public function __construct(
        #[Assert\Type('boolean')]
        public bool $isActive,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public string $title,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $description = null,
    ) {
    }
}
