<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Task;
use App\Domain\Model\Task\TaskModel;
use DateTimeImmutable;

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
}
