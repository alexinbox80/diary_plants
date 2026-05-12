<?php

namespace App\Infrastructure\Repository;

use Exception;
use App\Domain\Entity\Marker;
use Doctrine\ORM\QueryBuilder;

class MarkerRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('m', 'g')
            ->from(Marker::class, 'm')
            ->leftJoin('m.group', 'g')
            ->orderBy('m.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Marker[]
     * @throws Exception
     */
    public function getMarkersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param ?int $groupId
     * @return Marker[]
     * @throws Exception
     */
    public function getMarkersPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $queryBuilder = $this->getBaseQueryBuilder();

        $qb = $queryBuilder
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($groupId !== null) {
            $qb->where('m.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int|null $groupId
     * @param string|null $type
     * @return Marker[]
     */
    public function getMarkersForForm(?int $groupId = null, ?string $type = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('m')
            ->from(Marker::class, 'm')
            ->orderBy('m.letter', 'ASC');

        if ($groupId !== null) {
            $qb->where('m.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        if ($type !== null) {
            $qb->andWhere('m.type = :type')
                ->setParameter('type', $type);
        }

        return  $qb->getQuery()->getResult();
    }

    /**
     * @param int|null $groupId
     * @param string|null $type
     * @return array
     */
    public function getMarkersForDairy(?int $groupId = null, ?string $type = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('m')
            ->from(Marker::class, 'm')
            ->orderBy('m.letter', 'ASC');

        if ($groupId !== null) {
            $qb->where('m.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        if ($type !== null) {
            $qb->andWhere('m.type = :type')
                ->setParameter('type', $type);
        }

        return  $qb->getQuery()->getResult();
    }

    /**
     * @param int $markerId
     * @return Marker|null
     */
    public function find(int $markerId): ?Marker
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('m.id = :id')
            ->setParameter('id', $markerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Marker[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Marker[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $qb = $queryBuilder->select('m')
            ->from(Marker::class, 'm')
            ->orderBy('m.letter', 'ASC');

        if ($groupId !== null) {
            $qb->where('m.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $letter
     * @return Marker[]
     */
    public function findMarkersByLetter(string $letter): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('m.letter = :letter')
            ->setParameter('letter', $letter)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $color
     * @return Marker[]
     */
    public function findMarkersByColor(string $color): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('m.color = :color')
            ->setParameter('color', $color)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Marker $marker
     * @return int
     */
    public function create(Marker $marker): int
    {
        return $this->store($marker);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Marker $marker
     * @return void
     */
    public function remove(Marker $marker): void
    {
        $marker->setDeletedAt();
        $this->flush();
    }
}
