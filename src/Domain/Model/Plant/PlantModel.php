<?php

namespace App\Domain\Model\Plant;

use App\Domain\Model\OId;
use App\Domain\Model\Price;
use DateTime;

class PlantModel
{
    public function __construct(
        private readonly int $id,
        private readonly OId $oid,
        private readonly string $title,
        private readonly string $room,
        private readonly bool $isShown = true,
        private readonly ?string $description = null,
        private readonly ?DateTime $purchaseDate = null,
        private readonly ?DateTime $vaccinationDate = null,
        private readonly ?DateTime $plantingDate = null,
        private readonly ?string $seller = null,
        private readonly ?string $nursery = null,
        private readonly ?Price $price = null,
        private readonly ?Price $shippingCost = null,
        private readonly ?Price $packagingCost = null,
        private readonly ?string $soil = null,
        private readonly ?string $comment = null,
        private readonly DateTime $createdAt,
        private readonly DateTime $updatedAt
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

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function getNursery(): ?string
    {
        return $this->nursery;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function getShippingCost(): ?Price
    {
        return $this->shippingCost;
    }

    public function getPackagingCost(): ?Price
    {
        return $this->packagingCost;
    }

    public function getSoil(): ?string
    {
        return $this->soil;
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'oid' => $this->oid->toString(),
            'title' => $this->title,
            'room' => $this->room,
            'is_shown' => $this->isShown,
            'description' => $this->description,
            'purchase_date' => $this->purchaseDate->format('d.m.Y'),
            'vaccination_date' => $this->vaccinationDate->format('d.m.Y'),
            'planting_date' => $this->plantingDate->format('d.m.Y'),
            'seller' => $this->seller,
            'nursery' => $this->nursery,
            'price' => $this->price?->toString(),
            'shipping_cost' => $this->shippingCost?->toString(),
            'packaging_cost' => $this->packagingCost?->toString(),
            'soil' => $this->soil,
            'comment' => $this->comment,
            'created_at' => $this->createdAt->format('d.m.Y'),
            'updated_at' => $this->updatedAt->format('d.m.Y')
        ];
    }
}
