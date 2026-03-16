<?php

namespace App\Domain\Model\Marker;

use App\Domain\ValueObject\Enum\Usage\AttachableType;
use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Model\Group\GroupModel;

class MarkerModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly string $letter,
        private readonly string $color,
        private readonly string $type,
        private readonly ?string $description = null,
        private readonly ?string $colorDescription = null,
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

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getColorDescription(): ?string
    {
        return $this->colorDescription;
    }

    public function getType(): string
    {
        return $this->type;
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

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => '#',
            'group_id' => 'Идентификатор группы',
            'group_title' => 'Группа',
            'letter' => 'Обозначение',
            'color' => 'Цвет',
            'type' => 'Тип обозначения',
            'description' => 'Описание',
            'color_description' => 'Описание цвета',
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
            'letter' => $this->getLetter(),
            'color' => $this->getColor(),
            'type' => AttachableType::getLabel($this->getType()),
            'description' => $this->getDescription(),
            'color_description' => $this->getColorDescription(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
