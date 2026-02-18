<?php

namespace App\Controller\Web\Dashboard\Plant\CreatePlant\Input;

use DateTimeImmutable;
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
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $purchaseDate = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $vaccinationDate = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $plantingDate = null,
        public ?string $seller = null,
        public ?string $nursery = null,
        public ?string $price = null,
        public ?string $shippingCost = null,
        public ?string $packagingCost = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:255)]
        public ?string $soil = null,
        #[Assert\Type('boolean')]
        public bool $isSold = false,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $sellingDate = null,
        public ?string $sellingPrice = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null
    ) {
    }
}
