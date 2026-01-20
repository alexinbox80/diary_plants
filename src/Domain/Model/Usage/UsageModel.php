<?php

namespace App\Domain\Model\Usage;

use DateTimeImmutable;

class UsageModel
{
    public function __construct(
        private readonly int $id,
        private readonly DateTimeImmutable $useDate,
        private readonly int $plantId,
        private readonly ?string $comment = null,
        private readonly ?int $usableId = null,
        private readonly ?string $usableType = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUseDate(): DateTimeImmutable
    {
        return $this->useDate;
    }

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getUsableId(): ?int
    {
        return $this->usableId;
    }

    public function getUsableType(): ?string
    {
        return $this->usableType;
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
