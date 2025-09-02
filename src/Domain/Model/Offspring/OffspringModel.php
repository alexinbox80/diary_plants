<?php

namespace App\Domain\Model\Offspring;

use DateTime;

class OffspringModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $plantId,
        private readonly ?DateTime $fruitingDate = null,
        private readonly ?DateTime $floweringDate = null,
        private readonly ?int $mass = null,
        private readonly ?string $color = null,
        private readonly ?string $flavor = null,
        private readonly ?int $quantity = null,
        private readonly ?string $comment = null,
        private readonly DateTime $createdAt,
        private readonly DateTime $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getFruitingDate(): ?DateTime
    {
        return $this->fruitingDate;
    }

    public function getFloweringDate(): ?DateTime
    {
        return $this->floweringDate;
    }

    public function getMass(): ?int
    {
        return $this->mass;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function getFlavor(): ?string
    {
        return $this->flavor;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
