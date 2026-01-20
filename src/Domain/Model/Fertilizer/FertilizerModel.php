<?php

namespace App\Domain\Model\Fertilizer;

use DateTimeImmutable;

class FertilizerModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $plantId,
        private readonly string $title,
        private readonly int $quantity,
        private readonly string $letter,
        private readonly string $manufacturer,
        private readonly ?string $description = null,
        private readonly ?string $comment = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
