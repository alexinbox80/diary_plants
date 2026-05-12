<?php

namespace App\Domain\Model\Marker;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Marker;
use App\Domain\Model\Group\GroupModel;
use App\Domain\ValueObject\Enum\Timezone;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

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

    /**
     * @param Marker $marker
     * @param GroupModel|null $groupModel
     * @return MarkerModel
     */
    public static function fromEntity(Marker $marker, ?GroupModel $groupModel = null): self
    {
        return new self(
            $marker->getId(),
            $marker->getGroup()->getId(),
            $marker->getLetter(),
            $marker->getColor(),
            $marker->getType()->value,
            $marker->getDescription(),
            $marker->getColorDescription(),
            $groupModel,
            $marker->getCreatedAt(),
            $marker->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.marker.header.id',
            'icon_tag' => 'table.marker.header.icon_tag',
            'group_id' => 'table.marker.header.group_id',
            'group_title' => 'table.marker.header.group_title',
            'letter' => 'table.marker.header.letter',
            'color' => 'table.marker.header.color',
            'type' => 'table.marker.header.type',
            'description' => 'table.marker.header.description',
            'color_description' => 'table.marker.header.color_description',
            'created_at' => 'table.marker.header.created_at',
            'updated_at' => 'table.marker.header.updated_at'
        ];
    }

    public function toArray(?Timezone $tz = null): array
    {
        if (is_null($tz)) {
            $timezone = new DateTimeZone('Europe/Moscow');
        } else {
            $timezone = new DateTimeZone($tz->value);
        }

        return [
            'id' => $this->getId(),
            'icon_tag' => '',
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
