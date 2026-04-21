<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Analytic;

class AnalyticRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('a')
            ->from(Analytic::class, 'a')
            ->orderBy('a.updatedAt', 'DESC');
    }

    /**
     * @return Analytic[]
     * @throws \Exception
     */
    public function getAnalyticsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $analysticId
     * @return Analytic|null
     */
    public function find(int $analysticId): ?Analytic
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.id = :id')
            ->setParameter('id', $analysticId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Analytic[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $plantId
     * @return Analytic[]
     */
    public function findAnalyticsByPlantId(int $plantId): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.plant = :plantId')
            ->setParameter('plantId', $plantId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $groupId
     * @return Analytic[]
     */
    public function findAnalyticsByGroupId(int $groupId): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.group = :groupId')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $plantId
     * @return Analytic|null
     */
    public function findOneByPlantId(int $plantId): ?Analytic
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('a.plant = :plantId')
            ->setParameter('plantId', $plantId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Analytic $analytic
     * @return int
     */
    public function create(Analytic $analytic): int
    {
        return $this->store($analytic);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Analytic $analytic
     * @return void
     */
    public function remove(Analytic $analytic): void
    {
        $analytic->setDeletedAt();
        $this->flush();
    }
}
