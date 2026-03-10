<?php

namespace App\Domain\Model\Marker;

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
}
