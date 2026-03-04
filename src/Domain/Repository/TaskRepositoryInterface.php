<?php

namespace App\Domain\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Task;
use App\Domain\Model\Task\TaskModel;

interface TaskRepositoryInterface
{
    public function getTasksPaginated(int $page, int $perPage): array;
    public function find(int $taskId): ?Task;
    public function findModel(int $taskId): ?TaskModel;
    public function findAll(): array;
    public function findTasksByStatusId(string $statusId): array;
    public function findTasksByPlantId(string $plantId): array;
    public function findTasksByDate(DateTimeImmutable $date): array;
    public function create(Task $task): int;
    public function update(): void;
    public function remove(Task $task): void;
    public function toModel(Task $task, bool $addRelations = false): TaskModel;
}
