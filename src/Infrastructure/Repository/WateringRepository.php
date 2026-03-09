<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Watering;


class WateringRepository extends AbstractRepository
{
    /**
     * @return Watering[]
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('f')
            ->from(Watering::class, 'w')
            ->orderBy('w.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $wateringId
     * @return Watering|null
     */
    public function find(int $wateringId): ?Watering
    {
        $repository = $this->entityManager->getRepository(Watering::class);
        /** @var Watering|null $watering */
        $watering = $repository->find($wateringId);

        return $watering;
    }

    /**
     * @return Watering[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Watering::class)->findAll();
    }

    /**
     * @param string $type
     * @return Watering[]
     */
    public function findWateringsByType(string $type): array
    {
        return $this->entityManager->getRepository(Watering::class)->findBy(['type' => $type]);
    }

    /**
     * @param string $method
     * @return Watering[]
     */
    public function findWateringsByMethod(string $method): array
    {
        return $this->entityManager->getRepository(Watering::class)->findBy(['method' => $method]);
    }

    /**
     * @param DateTimeImmutable $wateredAt
     * @return Watering[]
     */
    public function findWateringsByWateredAt(DateTimeImmutable $wateredAt): array
    {
        return $this->entityManager->getRepository(Watering::class)->findBy(['watered_at' => $wateredAt]);
    }

    /**
     * @param Watering $watering
     * @return int
     */
    public function create(Watering $watering): int
    {
        return $this->store($watering);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Watering $watering
     * @return void
     */
    public function remove(Watering $watering): void
    {
        $watering->setDeletedAt();
        $this->flush();
    }
}
