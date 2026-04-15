<?php

namespace App\Domain\Model\Group;

use DateTimeZone;
use DateTimeImmutable;
use App\Domain\Entity\Group;

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
            'id' => '#',
            'title' => 'Заголовок',
            'is_active' => 'Группа активна',
            'description' => 'Описание',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления'
        ];
    }

    public function toArray(): array
    {
        $timezone = new DateTimeZone('Europe/Moscow');

        return [
            'id' => $this->getId(),
            'is_active' => $this->isActive() ? 'Да' : 'Нет',
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'created_at' => $this->getCreatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s'),
            'updated_at' => $this->getUpdatedAt()->setTimezone($timezone)->format('d.m.Y H:i:s')
        ];
    }
}
