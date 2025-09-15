<?php

namespace App\Domain\Model\Stimulant;

use DateTime;

class StimulantModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $plantId,
        private readonly string $title,
        private readonly int $quantity,
        private readonly DateTime $useDate,
        private readonly ?string $manufacturer = null,
        private readonly ?string $description = null,
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

    public function getUseDate(): DateTime
    {
        return $this->useDate;
    }

    public function getDescription(): ?string
    {
        return $this->description;
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
