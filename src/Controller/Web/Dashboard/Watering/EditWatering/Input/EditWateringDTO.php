<?php

namespace App\Controller\Web\Dashboard\Watering\EditWatering\Input;

use DateTimeImmutable;
use App\Domain\ValueObject\Enum\Watering\WaterType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Domain\ValueObject\Enum\Watering\WateringMethod;

class EditWateringDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $groupId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $markerId,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'integer', message: 'The value {{ value }} is not a valid integer.')]
        public int $amount,
        #[Assert\Choice(callback: [WaterType::class, 'getValues'])]
        public string $waterType,
        #[Assert\Choice(callback: [WateringMethod::class, 'getValues'])]
        public string $wateringMethod,
        #[Assert\Type(type: ['null', DateTimeImmutable::class])]
        public ?DateTimeImmutable $wateredAt = null,
        #[Assert\NotBlank]
        #[Assert\Type(type: 'string', message: 'The value {{ value }} is not a valid string.')]
        public string $temperature,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $description = null,
        #[Assert\Length(min:2)]
        #[Assert\Length(max:1024)]
        public ?string $comment = null,
    ) {
    }
}
