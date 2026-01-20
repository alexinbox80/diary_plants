<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Fertilizer;
use DateTimeImmutable;

class FertilizerRepository extends AbstractRepository
{
    /**
     * @return Fertilizer[]
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Fertilizer::class, 'f')
            ->orderBy('f.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $fertilizerId
     * @return Fertilizer|null
     */
    public function find(int $fertilizerId): ?Fertilizer
    {
        $repository = $this->entityManager->getRepository(Fertilizer::class);
        /** @var Fertilizer|null $fertilizer */
        $fertilizer = $repository->find($fertilizerId);

        return $fertilizer;
    }

    /**
     * @return Fertilizer[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Fertilizer::class)->findAll();
    }

    /**
     * @param string $title
     * @return Fertilizer[]
     */
    public function findFertilizersByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Fertilizer::class)->findBy(['title' => $title]);
    }

    /**
     * @param string $manufacturer
     * @return Fertilizer[]
     */
    public function findFertilizersByManufacturer(string $manufacturer): array
    {
        return $this->entityManager->getRepository(Fertilizer::class)->findBy(['manufacturer' => $manufacturer]);
    }

    /**
     * @param DateTimeImmutable $date
     * @return Fertilizer[]
     */
    public function findFertilizersByUseDate(DateTimeImmutable $date): array
    {
        return $this->entityManager->getRepository(Fertilizer::class)->findBy(['use_date' => $date]);
    }

    /**
     * @param Fertilizer $fertilizer
     * @return int
     */
    public function create(Fertilizer $fertilizer): int
    {
        return $this->store($fertilizer);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Fertilizer $fertilizer
     * @return void
     */
    public function remove(Fertilizer $fertilizer): void
    {
        $fertilizer->setDeletedAt();
        $this->flush();
    }
}
