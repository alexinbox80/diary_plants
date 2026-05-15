<?php

namespace App\Domain\Model\Group;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;
use App\Domain\ValueObject\Enum\Timezone;

class GroupModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $title,
        private readonly bool $isActive,
        private readonly ?string $description = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function fromEntity(Group $group): self
    {
        return new GroupModel(
            $group->getId(),
            $group->getTitle(),
            $group->isActive(),
            $group->getDescription(),
            $group->getCreatedAt(),
            $group->getUpdatedAt()
        );
    }

    public static function getTableHeaderRu(): array
    {
        return [
            'id' => 'table.group.header.id',
            'title' => 'table.group.header.title',
            'is_active' => 'table.group.header.is_active',
            'description' => 'table.group.header.description',
            'created_at' => 'table.group.header.created_at',
            'updated_at' => 'table.group.header.updated_at'
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
            'is_active' => $this->isActive(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
