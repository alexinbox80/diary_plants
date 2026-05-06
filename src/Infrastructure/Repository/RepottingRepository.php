<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Repotting;

class RepottingRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('r', 'p', 'g')
            ->from(Repotting::class, 'r')
            ->leftJoin('r.plant', 'p')
            ->leftJoin('p.analytic', 'a')
            ->addSelect('a')
            ->leftJoin('r.group', 'g')
            ->orderBy('r.updatedAt', 'DESC');
    }

    /**
     * @return Repotting[]
     * @throws \Exception
     */
    public function getRepottingsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $repottingId
     * @return Repotting|null
     */
    public function find(int $repottingId): ?Repotting
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('r.id = :id')
            ->setParameter('id', $repottingId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Repotting[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $repottedAt
     * @return Repotting[]
     */
    public function findRepottingsByRepottedAt(DateTimeImmutable $repottedAt): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('r.repottedAt = :repottedAt')
            ->setParameter('repottedAt', $repottedAt)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Repotting $repotting
     * @return int
     */
    public function create(Repotting $repotting): int
    {
        return $this->store($repotting);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Repotting $repotting
     * @return void
     */
    public function remove(Repotting $repotting): void
    {
        $repotting->setDeletedAt();
        $this->flush();
    }
}
