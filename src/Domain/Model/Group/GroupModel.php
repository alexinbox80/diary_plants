<?php

namespace App\Domain\Model\Group;

use DateTimeImmutable;

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

    public function getIsActive(): bool
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
}
