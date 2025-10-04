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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'plant_id' => 'Идентификатор растения',
            'fruiting_date' => 'Дата сбора',
            'flowering_date' => 'Дата цветения',
            'mass' => 'Масса гр.',
            'color' => 'Цвет',
            'flavor' => 'Вкус',
            'quantity' => 'Количество',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'attachment' => null,
            'plant_id' => $this->getPlantId(),
            'fruiting_date' => $this->getFruitingDate()?->format('d.m.Y'),
            'flowering_date' => $this->getFloweringDate()?->format('d.m.Y'),
            'mass' => $this->getMass(),
            'color' => $this->getColor(),
            'flavor' => $this->getFlavor(),
            'quantity' => $this->getQuantity(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->format('d.m.Y H:i:s'),
        ];
    }
}
