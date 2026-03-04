<?php

namespace App\Domain\Service;

use DateTimeImmutable;
use App\Domain\Entity\Task;
use App\Domain\Model\Task\TaskModel;
use Psr\Cache\InvalidArgumentException;
use App\Domain\Model\Task\CreateTaskModel;
use App\Domain\Model\Task\UpdateTaskModel;
use App\Domain\Repository\TaskRepositoryInterface;

class TaskService
{
    public function __construct(
        private readonly GroupService $groupService,
        private readonly StatusService $statusService,
        private readonly PlantService $plantService,
        private readonly TaskRepositoryInterface $taskRepository
    ) {
    }

    /**
     * @param int $taskId
     * @return ?Task
     */
    public function find(int $taskId): ?Task
    {
        return $this->taskRepository->find($taskId);
    }

    /**
     * @return Task[]
     */
    public function findAll(): array
    {
        return $this->taskRepository->findAll();
    }

    /**
     * @param string $statusId
     * @return TaskModel[]
     */
    public function findTasksByStatusId(string $statusId): array
    {
        return $this->taskRepository->findTasksByStatusId($statusId);
    }

    /**
     * @param string $plantId
     * @return TaskModel[]
     */
    public function findTasksByPlantId(string $plantId): array
    {
        return $this->taskRepository->findTasksByStatusId($plantId);
    }

    /**
     * @param DateTimeImmutable $date
     * @return TaskModel[]
     */
    public function findTasksByDate(DateTimeImmutable $date): array
    {
        return $this->taskRepository->findTasksByDate($date);
    }

    /**
     * @return TaskModel[]
     * @throws InvalidArgumentException
     */
    public function getTasksPaginated(int $page, int $perPage): array
    {
        return $this->taskRepository->getTasksPaginated($page, $perPage);
    }

    /**
     * @param CreateTaskModel $createTaskModel
     * @return TaskModel
     * @throws InvalidArgumentException
     */
    public function create(CreateTaskModel $createTaskModel): TaskModel
    {
        $group = $this->groupService->find($createTaskModel->groupId);
        $status = $this->statusService->find($createTaskModel->statusId);
        $plant = $this->plantService->find($createTaskModel->plantId);

        $task = new Task(
            $group,
            $status,
            $plant,
            $createTaskModel->date,
            $createTaskModel->description
        );

        $this->taskRepository->create($task);

        return $this->taskRepository->toModel($task);
    }

    /**
     * @param Task $task
     * @param UpdateTaskModel $updateTaskModel
     * @return TaskModel
     * @throws InvalidArgumentException
     */
    public function update(Task $task, UpdateTaskModel $updateTaskModel): TaskModel
    {
        $group = $this->groupService->find($updateTaskModel->groupId);
        $status = $this->statusService->find($updateTaskModel->statusId);
        $plant = $this->plantService->find($updateTaskModel->plantId);

        $task->changeFields(
            $group,
            $status,
            $plant,
            $updateTaskModel->date,
            $updateTaskModel->description,
        );

        $this->taskRepository->update();

        return $this->taskRepository->toModel($task);
    }

    /**
     * @param int $taskId
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeById(int $taskId): void
    {
        $task = $this->taskRepository->find($taskId);
        if ($task !== null) {
            $this->taskRepository->remove($task);
        }
    }

    /**
     * @param Task $task
     * @return void
     * @throws InvalidArgumentException
     */
    public function removeTask(Task $task): void
    {
        $this->taskRepository->remove($task);
    }
}
