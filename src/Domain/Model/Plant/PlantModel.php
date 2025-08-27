<?php

namespace App\Domain\Model\Plant;

use App\Domain\Model\OId;
use App\Domain\Model\Price;
use DateTime;

class PlantModel
{
    public function __construct(
        public readonly int $id,
        public readonly OId $oid,
        public readonly string $title,
        public readonly string $room,
        public readonly bool $isShown = true,
        public readonly ?string $description = null,
        public readonly ?DateTime $purchaseDate = null,
        public readonly ?DateTime $vaccinationDate = null,
        public readonly ?DateTime $plantingDate = null,
        public readonly ?string $manufacturer = null,
        public readonly ?Price $price = null,
        public readonly ?string $soil = null,
        public readonly DateTime $createdAt,
        public readonly DateTime $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOid(): OId
    {
        return $this->oid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getRoom(): string
    {
        return $this->room;
    }

    public function isShown(): bool
    {
        return $this->isShown;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getPurchaseDate(): ?DateTime
    {
        return $this->purchaseDate;
    }

    public function getVaccinationDate(): ?DateTime
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTime
    {
        return $this->plantingDate;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function getSoil(): ?string
    {
        return $this->soil;
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
