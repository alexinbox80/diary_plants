<?php

namespace App\Domain\Model\Plant;

use App\Domain\ValueObject\Price;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CreatePlantModel
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public readonly int $groupId,
        #[Assert\NotBlank]
        public readonly string $title,
        #[Assert\NotBlank]
        public readonly string $room,
        public readonly bool $isShown,
        public readonly ?string $description = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $purchaseDate = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $vaccinationDate = null,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $plantingDate = null,
        public readonly ?string $seller = null,
        public readonly ?string $nursery = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $price = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $shippingCost = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $packagingCost = null,
        public readonly ?string $soil = null,
        public readonly bool $isSold = false,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public readonly ?DateTimeImmutable $sellingDate = null,
        #[Assert\Type(type: ['null', Price::class])]
        public readonly ?Price $sellingPrice = null,
        public readonly ?string $comment = null
    ) {
    }
}
