<?php

namespace App\Domain\Model\Task;

use DateTimeImmutable;

class TaskModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $statusId,
        private readonly int $plantId,
        private readonly DateTimeImmutable $date,
        private readonly ?string $description = null,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatusId(): int
    {
        return $this->statusId;
    }

    public function getPlantId(): int
    {
        return $this->plantId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getDescription(): string
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
