<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use App\Domain\Model\Task\TaskModel;
use App\Domain\Repository\TaskRepositoryInterface;
use DateTimeImmutable;

class TaskRepositoryDecorator implements TaskRepositoryInterface
{
    public function __construct(
        private readonly TaskRepository $taskRepository,
    ) {
    }

    /**
     * @return TaskModel[]
     */
    public function getTasksPaginated(int $page, int $perPage): array
    {
        $tasks = $this->taskRepository->getTasksPaginated($page, $perPage);

        return array_map(
            static fn (Task $task): TaskModel => new TaskModel(
                $task->getId(),
                $task->getStatus()->getId(),
                $task->getPlant()->getId(),
                $task->getDate(),
                $task->getDescription(),
                $task->getCreatedAt(),
                $task->getUpdatedAt()
            ),
            $tasks
        );
    }

    /**
     * @param int $taskId
     * @return Task|null
     */
    public function find(int $taskId): ?Task
    {
        return $this->taskRepository->find($taskId);
    }

    /**
     * @param int $taskId
     * @return TaskModel|null
     */
    public function findModel(int $taskId): ?TaskModel
    {
        $task = $this->taskRepository->find($taskId);

        return new TaskModel(
            $task->getId(),
            $task->getStatus()->getId(),
            $task->getPlant()->getId(),
            $task->getDate(),
            $task->getDescription(),
            $task->getCreatedAt(),
            $task->getUpdatedAt()
        );
    }

    /**
     * @return TaskModel[]
     */
    public function findAll(): array
    {
        $tasks = $this->taskRepository->findAll();

        return array_map(
            static fn (Task $task): TaskModel => new TaskModel(
                $task->getId(),
                $task->getStatus()->getId(),
                $task->getPlant()->getId(),
                $task->getDate(),
                $task->getDescription(),
                $task->getCreatedAt(),
                $task->getUpdatedAt()
            ),
            $tasks
        );
    }

    /**
     * @param string $statusId
     * @return TaskModel[]
     */
    public function findTasksByStatusId(string $statusId): array
    {
        $tasks = $this->taskRepository->findPlantsByStatusId($statusId);

        return array_map(
            static fn (Task $task): TaskModel => new TaskModel(
                $task->getId(),
                $task->getStatus()->getId(),
                $task->getPlant()->getId(),
                $task->getDate(),
                $task->getDescription(),
                $task->getCreatedAt(),
                $task->getUpdatedAt()
            ),
            $tasks
        );
    }

    /**
     * @param string $plantId
     * @return TaskModel[]
     */
    public function findTasksByPlantId(string $plantId): array
    {
        $tasks = $this->taskRepository->findPlantsByPlantId($plantId);

        return array_map(
            static fn (Task $task): TaskModel => new TaskModel(
                $task->getId(),
                $task->getStatus()->getId(),
                $task->getPlant()->getId(),
                $task->getDate(),
                $task->getDescription(),
                $task->getCreatedAt(),
                $task->getUpdatedAt()
            ),
            $tasks
        );
    }

    /**
     * @param DateTimeImmutable $date
     * @return TaskModel[]
     */
    public function findTasksByDate(DateTimeImmutable $date): array
    {
        $tasks = $this->taskRepository->findPlantsByDate($date);

        return array_map(
            static fn (Task $task): TaskModel => new TaskModel(
                $task->getId(),
                $task->getStatus()->getId(),
                $task->getPlant()->getId(),
                $task->getDate(),
                $task->getDescription(),
                $task->getCreatedAt(),
                $task->getUpdatedAt()
            ),
            $tasks
        );
    }

    /**
     * @param Task $task
     * @return int
     */
    public function create(Task $task): int
    {
        return $this->taskRepository->create($task);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->taskRepository->update();
    }

    /**
     * @param Task $task
     * @return void
     */
    public function remove(Task $task): void
    {
        $this->taskRepository->remove($task);
    }
}
