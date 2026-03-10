<?php

namespace App\Domain\Model\Fertilizer;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Marker\MarkerModel;
use App\Domain\Model\Interfaces\AttachableModelInterface;

class FertilizerModel implements AttachableModelInterface
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $markerId,
        private readonly string $title,
        private readonly int $quantity,
        private readonly string $letter,
        private readonly string $manufacturer,
        private readonly ?string $description = null,
        private readonly ?string $comment = null,
        private readonly ?GroupModel $group = null,
        private readonly ?MarkerModel $marker = null,
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

    public function getMarkerId(): int
    {
        return $this->markerId;
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

    public function getGroup(): ?GroupModel
    {
        return $this->group;
    }

    public function getMarker(): ?MarkerModel
    {
        return $this->marker;
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
            'group_title' => 'Группа',
            'marker_id' => 'Идентификатор маркера',
            'marker_letter' => 'Обозначение',
            'title' => 'Заголовок',
            'quantity' => 'Количество',
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
            'group_title' => $this->getGroup()?->getTitle(),
            'marker_id' => $this->getMarkerId(),
            'marker_letter' => $this->getMarker()?->getLetter(),
            'title' => $this->getTitle(),
            'quantity' => $this->getQuantity(),
            'letter' => $this->getLetter(),
            'manufacturer' => $this->getManufacturer(),
            'description' => $this->getDescription(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
