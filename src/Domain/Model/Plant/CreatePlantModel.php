<?php

namespace App\Domain\Model\Plant;

use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\Model\Price;
use DateTime;

class CreatePlantModel
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $title,
        #[Assert\NotBlank]
        public readonly string $room,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'bool', message: 'The value must be a boolean.')]
        public readonly bool $isShown = true,
        public readonly ?string $description = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public readonly ?DateTime $purchaseDate = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public readonly ?DateTime $vaccinationDate = null,
        #[Assert\Type(type: ['null', DateTime::class])]
        public readonly ?DateTime $plantingDate = null,
        public readonly ?string $seller = null,
        public readonly ?string $nursery = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $price = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $shippingCost = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $packagingCost = null,
        public readonly ?string $soil = null,
        public readonly ?string $comment = null
    ) {
    }
}
