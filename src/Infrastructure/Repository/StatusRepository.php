<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Status;

class StatusRepository extends AbstractRepository
{
    /**
     * @return Status[]
     */
    public function getStatusesPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Status::class, 't')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $statusId
     * @return Status|null
     */
    public function find(int $statusId): ?Status
    {
        $repository = $this->entityManager->getRepository(Status::class);
        /** @var Status|null $status */
        $status = $repository->find($statusId);

        return $status;
    }

    /**
     * @return Status[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Status::class)->findAll();
    }

    /**
     * @param string $letter
     * @return Status[]
     */
    public function findStatusesByLetter(string $letter): array
    {
        return $this->entityManager->getRepository(status::class)->findBy(['letter' => $letter]);
    }

    /**
     * @param string $color
     * @return Status[]
     */
    public function findStatusesByColor(string $color): array
    {
        return $this->entityManager->getRepository(status::class)->findBy(['color' => $color]);
    }

    /**
     * @param Status $status
     * @return int
     */
    public function create(Status $status): int
    {
        return $this->store($status);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Status $status
     * @return void
     */
    public function remove(Status $status): void
    {
        $status->setDeletedAt();
        $this->flush();
    }
}
