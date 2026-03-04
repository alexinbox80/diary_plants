<?php

namespace App\Domain\Model\Task;

use DateTimeImmutable;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Status\StatusModel;

class TaskModel
{
    public function __construct(
        private readonly int $id,
        private readonly int $groupId,
        private readonly int $statusId,
        private readonly int $plantId,
        private readonly DateTimeImmutable $date,
        private readonly ?string $description = null,
        private readonly ?GroupModel $group = null,
        private readonly ?StatusModel $status = null,
        private readonly ?PlantModel $plant = null,
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

    public function getGroup(): GroupModel
    {
        return $this->group;
    }

    public function getStatus(): StatusModel
    {
        return $this->status;
    }

    public function getPlant(): PlantModel
    {
        return $this->plant;
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
