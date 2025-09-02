<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Task;
use DateTime;

class TaskRepository extends AbstractRepository
{
    /**
     * @return Task[]
     */
    public function getTasksPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Task::class, 't')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $taskId
     * @return Task|null
     */
    public function find(int $taskId): ?Task
    {
        $repository = $this->entityManager->getRepository(Task::class);
        /** @var Task|null $task */
        $task = $repository->find($taskId);

        return $task;
    }

    /**
     * @return Task[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Task::class)->findAll();
    }

    /**
     * @param string $statusId
     * @return Task[]
     */
    public function findPlantsByStatusId(string $statusId): array
    {
        return $this->entityManager->getRepository(Task::class)->findBy(['status_id' => $statusId]);
    }

    /**
     * @param string $plantId
     * @return Task[]
     */
    public function findPlantsByPlantId(string $plantId): array
    {
        return $this->entityManager->getRepository(Task::class)->findBy(['plant_id' => $plantId]);
    }

    /**
     * @param DateTime $date
     * @return Task[]
     */
    public function findPlantsByDate(DateTime $date): array
    {
        return $this->entityManager->getRepository(Task::class)->findBy(['date' => $date]);
    }

    /**
     * @param Task $task
     * @return int
     */
    public function create(Task $task): int
    {
        return $this->store($task);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Task $task
     * @return void
     */
    public function remove(Task $task): void
    {
        $task->setDeletedAt();
        $this->flush();
    }
}
