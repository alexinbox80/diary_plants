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
        private readonly ?string $qrCodeBase64 = null,
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

    public function getQrCodeBase64(): ?string
    {
        return $this->qrCodeBase64;
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'oid' => 'Универсальный идентификатор',
            'title' => 'Название',
            'room' => 'Помещение',
            'is_shown' => 'Показать',
            'description' => 'Описание',
            'qr_code_base64' => 'QR код',
            'purchase_date' => 'Дата покупки',
            'vaccination_date' => 'Дата прививки',
            'planting_date' => 'Дата посадки',
            'seller' => 'Продавец',
            'nursery' => 'Питомник',
            'price' => 'Стоимость',
            'shipping_cost' => 'Стоимость доставки',
            'packaging_cost' => 'Стоимость упаковки',
            'soil' => 'Грунт',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'oid' => $this->oid->toString(),
            'title' => $this->getTitle(),
            'room' => $this->getRoom(),
            'is_shown' => $this->isShown() ? 'Да' : 'Нет',
            'description' => $this->getDescription(),
            'qr_code_base64' => $this->getQrCodeBase64(),
            'purchase_date' => $this->purchaseDate->format('d.m.Y'),
            'vaccination_date' => $this->vaccinationDate->format('d.m.Y'),
            'planting_date' => $this->plantingDate->format('d.m.Y'),
            'seller' => $this->getSeller(),
            'nursery' => $this->getNursery(),
            'price' => $this->price?->toString(),
            'shipping_cost' => $this->shippingCost?->toString(),
            'packaging_cost' => $this->packagingCost?->toString(),
            'soil' => $this->getSoil(),
            'comment' => $this->getComment(),
            'created_at' => $this->createdAt->format('d.m.Y H:i:s'),
            'updated_at' => $this->updatedAt->format('d.m.Y H:i:s'),
        ];
    }
}
