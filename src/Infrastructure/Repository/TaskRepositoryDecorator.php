<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Task;
use App\Domain\Model\Task\TaskModel;
use App\Domain\Model\Group\GroupModel;
use App\Domain\Model\Plant\PlantModel;
use App\Domain\Model\Status\StatusModel;
use App\Domain\Repository\TaskRepositoryInterface;

class TaskRepositoryDecorator implements TaskRepositoryInterface
{
    public function __construct(
        private readonly GroupRepositoryDecorator $groupRepository,
        private readonly StatusRepositoryDecorator $statusRepository,
        private readonly PlantRepositoryDecorator $plantRepository,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    /**
     * @return TaskModel[]
     */
    public function getTasksPaginated(int $page, int $perPage): array
    {
        $tasksPaginated = $this->taskRepository->getTasksPaginated($page, $perPage);

        if (!is_array($tasksPaginated['items'])) {
            throw new \InvalidArgumentException('Expected array for tasks');
        }

        $tasksModel = array_map(
            fn (Task $task): TaskModel => $this->toModel($task, true),
            $tasksPaginated['items']
        );

        return [
            'tasksModel' => $tasksModel,
            'pagination' => $tasksPaginated['pagination']
        ];
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

        return $this->toModel($task);
    }

    /**
     * @return TaskModel[]
     */
    public function findAll(): array
    {
        $tasks = $this->taskRepository->findAll();

        return array_map(
            fn (Task $task): TaskModel => $this->toModel($task, true),
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
            fn (Task $task): TaskModel => $this->toModel($task),
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
            fn (Task $task): TaskModel => $this->toModel($task),
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
            fn (Task $task): TaskModel => $this->toModel($task),
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

    /**
     * @param Task $task
     * @param bool $addRelations
     * @return TaskModel
     */
    public function toModel(Task $task, bool $addRelations = false): TaskModel
    {
        $groupModel = null;
        $statusModel = null;
        $plantModel = null;

        if ($addRelations) {
            $groupModel = $this->groupRepository->findModel($task->getGroup()->getId());
            $statusModel = $this->statusRepository->findModel($task->getStatus()->getId());
            $plantModel = $this->plantRepository->findModel($task->getPlant()->getId());
        }

        return self::makeTaskModel($task, $groupModel, $statusModel, $plantModel);
    }

    /**
     * @param Task $task
     * @param GroupModel|null $groupModel
     * @param StatusModel|null $statusModel
     * @param PlantModel|null $plantModel
     * @return TaskModel
     */
    static function makeTaskModel(Task $task, ?GroupModel $groupModel = null, ?StatusModel $statusModel = null, ?PlantModel $plantModel = null): TaskModel
    {
        return new TaskModel(
            $task->getId(),
            $task->getGroup()->getId(),
            $task->getStatus()->getId(),
            $task->getPlant()->getId(),
            $task->getDate(),
            $task->getDescription(),
            $groupModel,
            $statusModel,
            $plantModel,
            $task->getCreatedAt(),
            $task->getUpdatedAt()
        );
    }
}
