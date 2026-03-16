<?php

namespace App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input;

use Symfony\Component\Validator\Constraints as Assert;

class EditFertilizerDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $markerId,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public string $title,
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        #[Assert\Positive]
        public int $amount,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:50)]
        public string $applicationRate,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public string $manufacturer,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $description = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null
    ) {
    }
}
