<?php

namespace App\Domain\Model\Plant;

use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\Model\OId;
use App\Domain\Model\Price;
use DateTime;

class CreatePlantModel
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly OId $oid,
        #[Assert\NotBlank]
        public readonly string $title,
        #[Assert\NotBlank]
        public readonly string $room,
        #[Assert\NotBlank]
        public readonly bool $isShown = true,
        public readonly ?string $description = null,
        public readonly ?DateTime $purchaseDate = null,
        public readonly ?DateTime $vaccinationDate = null,
        public readonly ?DateTime $plantingDate = null,
        public readonly ?string $manufacturer = null,
        public readonly ?Price $price = null,
        public readonly ?string $soil = null,
    ) {
    }
}
