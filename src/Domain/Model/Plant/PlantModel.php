<?php

namespace App\Domain\Model\Plant;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Plant;
use App\Domain\ValueObject\OId;
use App\Domain\ValueObject\Price;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Attachment\AttachmentModel;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class PlantModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly OId $oid,
        private readonly string $title,
        private readonly string $room,
        private readonly bool $isShown = true,
        private readonly array $attachment = [],
        private readonly ?string $description = null,
        private readonly ?string $qrCodeLink = null,
        private readonly ?DateTimeImmutable $purchaseDate = null,
        private readonly ?DateTimeImmutable $vaccinationDate = null,
        private readonly ?DateTimeImmutable $plantingDate = null,
        private readonly ?string $seller = null,
        private readonly ?string $nursery = null,
        private readonly ?Price $price = null,
        private readonly ?Price $shippingCost = null,
        private readonly ?Price $packagingCost = null,
        private readonly ?string $soil = null,
        private readonly bool $isSold = false,
        private readonly ?DateTimeImmutable $sellingDate = null,
        private readonly ?Price $sellingPrice = null,
        private readonly ?string $comment = null,
        private readonly ?GroupModel $group = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
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

    public function getQrCodeLink(): ?string
    {
        return $this->qrCodeLink;
    }

    public function getPurchaseDate(): ?DateTimeImmutable
    {
        return $this->purchaseDate;
    }

    public function getVaccinationDate(): ?DateTimeImmutable
    {
        return $this->vaccinationDate;
    }

    public function getPlantingDate(): ?DateTimeImmutable
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

    public function isSold(): bool
    {
        return $this->isSold;
    }

    public function getSellingDate(): ?DateTimeImmutable
    {
        return $this->sellingDate;
    }

    public function getSellingPrice(): ?Price
    {
        return $this->sellingPrice;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return AttachmentModel[]
     */
    public function getAttachment(): array
    {
        return $this->attachment;
    }

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param Plant $plant
     * @param array $attachmentModels
     * @param GroupModel|null $groupModel
     * @return PlantModel
     */
    public static function fromEntity(
        Plant $plant,
        array $attachmentModels = [],
        ?GroupModel $groupModel = null
    ): self {
        return new self(
            $plant->getId(),
            $plant->getGroup()->getId(),
            $plant->getPlantIdentifier()->getOid(),
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $attachmentModels,
            $plant->getDescription(),
            $plant->getPlantIdentifier()->getQrCodeLink(),
            $plant->getPurchaseInfo()->getPurchaseDate(),
            $plant->getLifeCycle()->getVaccinationDate(),
            $plant->getLifeCycle()->getPlantingDate(),
            $plant->getPurchaseInfo()->getSeller(),
            $plant->getPurchaseInfo()->getNursery(),
            $plant->getPurchaseInfo()->getPrice(),
            $plant->getPurchaseInfo()->getShippingCost(),
            $plant->getPurchaseInfo()->getPackagingCost(),
            $plant->getLifeCycle()->getSoil(),
            $plant->getSalesInfo()->isSold(),
            $plant->getSalesInfo()->getSellingDate(),
            $plant->getSalesInfo()->getSellingPrice(),
            $plant->getComment(),
            $groupModel,
            $plant->getCreatedAt(),
            $plant->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'oid' => 'Универсальный идентификатор',
            'img_gallery' => 'Изображение',
            'title' => 'Название',
            'room' => 'Помещение',
            'is_shown' => 'Показать',
            'description' => 'Описание',
            'img_qr_code_link' => 'QR код',
            'purchase_date' => 'Дата покупки',
            'vaccination_date' => 'Дата прививки',
            'planting_date' => 'Дата посадки',
            'seller' => 'Продавец',
            'nursery' => 'Питомник',
            'price' => 'Стоимость',
            'shipping_cost' => 'Стоимость доставки',
            'packaging_cost' => 'Стоимость упаковки',
            'soil' => 'Грунт',
            'is_sold' => 'Продано',
            'selling_date' => 'Дата продажи',
            'selling_price' => 'Стоимость при продаже',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        $filtered = array_filter($this->getAttachment(), fn ($attachment) => $attachment->getMimeType() !== null);
        $imgGallery = array_map(fn ($attachment) => $attachment->toArray(), $filtered);

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'group_title' => $this->getGroup()?->getTitle(),
            'oid' => $this->getOid()->toString(),
            'img_gallery' => $imgGallery,
            'title' => $this->getTitle(),
            'room' => $this->getRoom(),
            'is_shown' => $this->isShown() ? 'Да' : 'Нет',
            'description' => $this->getDescription(),
            'img_qr_code_link' => (!empty($this->getQrCodeLink())) ? $this->getQrCodeLink() : null,
            'purchase_date' => $this->getPurchaseDate()?->format('d.m.Y'),
            'vaccination_date' => $this->getVaccinationDate()?->format('d.m.Y'),
            'planting_date' => $this->getPlantingDate()?->format('d.m.Y'),
            'seller' => $this->getSeller(),
            'nursery' => $this->getNursery(),
            'price' => $this->price?->toString(),
            'shipping_cost' => $this->getShippingCost()?->toString(),
            'packaging_cost' => $this->getPackagingCost()?->toString(),
            'soil' => $this->getSoil(),
            'is_sold' => $this->isSold() ? 'Да' : 'Нет',
            'selling_date' => $this->getSellingDate()?->format('d.m.Y'),
            'selling_price' => $this->getSellingPrice()?->toString(),
            'comment' => $this->getComment(),
            'attachment' => $this->getAttachment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
        ];
    }
}
