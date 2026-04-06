<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Watering;


class WateringRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('w', 'g', 'm')
            ->from(Watering::class, 'w')
            ->leftJoin('w.group', 'g')
            ->leftJoin('w.marker', 'm')
            ->orderBy('w.updatedAt', 'DESC');
    }

    /**
     * @param int $groupId
     * @return Watering[]
     */
    public function getWateringsForDairy(int $groupId): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('w.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Watering[]
     * @throws \Exception
     */
    public function getWateringsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $wateringId
     * @return Watering|null
     */
    public function find(int $wateringId): ?Watering
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('w.id = :id')
            ->setParameter('id', $wateringId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Watering[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $type
     * @return Watering[]
     */
    public function findWateringsByType(string $type): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('w.type = :type')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $method
     * @return Watering[]
     */
    public function findWateringsByMethod(string $method): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('w.method = :method')
            ->setParameter('method', $method)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $wateredAt
     * @return Watering[]
     */
    public function findWateringsByWateredAt(DateTimeImmutable $wateredAt): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('m.wateredAt = :wateredAt')
            ->setParameter('wateredAt', $wateredAt)
            ->getQuery()
            ->getResult();
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
