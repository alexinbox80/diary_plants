<?php

namespace App\Domain\Model\Plant;

use App\Domain\ValueObject\Price;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class UpdatePlantModel
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly string $title,
        #[Assert\NotBlank]
        public readonly string $room,
        public readonly bool $isShown = true,
        public readonly ?string $description = null,
        public readonly ?DateTimeImmutable $purchaseDate = null,
        public readonly ?DateTimeImmutable $vaccinationDate = null,
        public readonly ?DateTimeImmutable $plantingDate = null,
        public readonly ?string $seller = null,
        public readonly ?string $nursery = null,
        public readonly ?Price $price = null,
        public readonly ?Price $shippingCost = null,
        public readonly ?Price $packagingCost = null,
        public readonly ?string $soil = null,
        public readonly ?string $comment = null
    ) {
    }
}
