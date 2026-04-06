<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Fertilizer;

class FertilizerRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('f', 'g', 'm')
            ->from(Fertilizer::class, 'f')
            ->leftJoin('f.group', 'g')
            ->leftJoin('f.marker', 'm')
            ->orderBy('f.updatedAt', 'DESC');
    }

    /**
     * @param int $groupId
     * @return Fertilizer[]
     */
    public function getFertilizersForDairy(int $groupId): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('f.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Fertilizer[]
     * @throws \Exception
     */
    public function getFertilizersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $fertilizerId
     * @return Fertilizer|null
     */
    public function find(int $fertilizerId): ?Fertilizer
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('f.id = :id')
            ->setParameter('id', $fertilizerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Fertilizer[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $title
     * @return Fertilizer[]
     */
    public function findFertilizersByTitle(string $title): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('f.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $manufacturer
     * @return Fertilizer[]
     */
    public function findFertilizersByManufacturer(string $manufacturer): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('f.manufacturer = :manufacturer')
            ->setParameter('manufacturer', $manufacturer)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $date
     * @return Fertilizer[]
     */
    public function findFertilizersByUseDate(DateTimeImmutable $date): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('f.useDate = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
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
