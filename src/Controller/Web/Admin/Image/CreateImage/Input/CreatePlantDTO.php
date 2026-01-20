<?php

namespace App\Controller\Web\Admin\Image\CreateImage\Input;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePlantDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public ?string $title = null,
        #[Assert\NotBlank]
        #[Assert\Length(min:2)]
        #[Assert\Length(max:64)]
        public ?string $room = null,
        #[Assert\Type('boolean')]
        public ?bool $isShown = true,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $description = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public ?DateTime $purchaseDate = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public ?DateTime $vaccinationDate = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public ?DateTime $plantingDate = null,
        public ?string $seller = null,
        public ?string $nursery = null,
        public ?string $price = null,
        public ?string $shippingCost = null,
        public ?string $packagingCost = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public ?string $soil = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null
    ) {
    }
}
