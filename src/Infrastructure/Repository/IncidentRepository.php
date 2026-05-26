<?php

namespace App\Infrastructure\Repository;

use Exception;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Incident;

class IncidentRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('i')
            ->from(Incident::class, 'i')
            ->orderBy('i.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Incident[]
     * @throws Exception
     */
    public function getIncidentsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $incidentId
     * @return Incident|null
     */
    public function find(int $incidentId): ?Incident
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('i.id = :id')
            ->setParameter('id', $incidentId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Incident[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param ?int $userId
     * @return Incident[]
     */
    public function findAllByUserId(?int $userId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('i')
            ->from(Incident::class, 'i')
            ->orderBy('i.publicCode', 'ASC');

        if ($userId !== null) {
            $qb->where('i.userId = :userId')
                ->setParameter('userId', $userId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $publicCode
     * @return ?Incident
     */
    public function findIncidentByPublicCode(string $publicCode): ?Incident
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('i.publicCode = :publicCode')
            ->setParameter('publicCode', $publicCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param Incident $incident
     * @return int
     */
    public function create(Incident $incident): int
    {
        return $this->store($incident);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Incident $incident
     * @return void
     */
    public function remove(Incident $incident): void
    {
        $incident->setDeletedAt();
        $this->flush();
    }
}
