<?php

namespace App\Domain\Model\Task;

use DateTime;

class TaskModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $statusId,
        private readonly int $plantId,
        private readonly DateTime $date,
        private readonly ?string $description = null,
        private readonly DateTime $createdAt,
        private readonly DateTime $updatedAt
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

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
