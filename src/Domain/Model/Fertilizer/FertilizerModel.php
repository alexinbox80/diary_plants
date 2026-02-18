<?php

namespace App\Domain\Model\Fertilizer;

use DateTimeZone;
use DateTimeImmutable;

class FertilizerModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
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

    public function getGroupId(): int
    {
        return $this->groupId;
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'plant_id' => 'Идентификатор растения',
            'title' => 'Заголовок',
            'letter' => 'Буква обозначения',
            'manufacturer' => 'Изготовитель',
            'description' => 'Описание',
            'comment' => 'Комментарий',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        return [
            'id' => $this->getId(),
            'group_id' => $this->getGroupId(),
            'plant_id' => $this->getPlantId(),
            'title' => $this->getTitle(),
            'letter' => $this->getLetter(),
            'manufacturer' => $this->getManufacturer(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
