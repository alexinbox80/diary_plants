<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Plant;
use App\Domain\Model\Price;

class PlantRepository extends AbstractRepository
{
    /**
     * @param int $page
     * @param int $perPage
     * @return Plant[]
     */
    public function getPlantsPaginated(int $page, int $perPage): array
    {
        $query = $this->entityManager
            ->getRepository(Plant::class)
            ->createQueryBuilder('p')
            ->select('p')
            ->getQuery();

        return $this->getPaginatedResults($query, $page, $perPage);
    }

    /**
     * @return int
     */
    public function getPlantsCount(): int
    {
        $repository = $this->entityManager->getRepository(Plant::class);

        return $repository->count([]);
    }

    /**
     * @param int $plantId
     * @return Plant|null
     */
    public function find(int $plantId): ?Plant
    {
        $repository = $this->entityManager->getRepository(Plant::class);
        /** @var Plant|null $plant */
        $plant = $repository->find($plantId);

        return $plant;
    }

    /**
     * @return Plant[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Plant::class)->findAll();
    }

    /**
     * @param string $title
     * @return Plant[]
     */
    public function findPlantsByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Plant::class)->findBy(['title' => $title]);
    }

    /**
     * @param Price $price
     * @return Plant[]
     */
    public function findPlantsByPrice(Price $price): array
    {
        return $this->entityManager->getRepository(Plant::class)->findBy(['price' => $price]);
    }

    /**
     * @param Plant $plant
     * @return int
     */
    public function create(Plant $plant): int
    {
        return $this->store($plant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Plant $plant
     * @return void
     */
    public function remove(Plant $plant): void
    {
        $plant->setDeletedAt();
        $this->flush();
    }
}
