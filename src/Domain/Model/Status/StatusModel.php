<?php

namespace App\Domain\Model\Status;

use DateTimeImmutable;

class StatusModel
{
    public function __construct(
        private readonly int $id,
        private readonly string $letter,
        private readonly string $color,
        private readonly ?string $description = null,
        private readonly ?string $colorDescription = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
