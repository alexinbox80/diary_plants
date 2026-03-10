<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Marker;

class MarkerRepository extends AbstractRepository
{
    /**
     * @return Marker[]
     */
    public function getMarkersPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Marker::class, 'm')
            ->orderBy('m.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int|null $groupId
     * @param string|null $type
     * @return array
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
     * @param int $markerId
     * @return Marker|null
     */
    public function find(int $markerId): ?Marker
    {
        $repository = $this->entityManager->getRepository(Marker::class);
        /** @var Marker|null $marker */
        $marker = $repository->find($markerId);

        return $marker;
    }

    /**
     * @return Marker[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Marker::class)->findAll();
    }

    /**
     * @param string $letter
     * @return Marker[]
     */
    public function findMarkersByLetter(string $letter): array
    {
        return $this->entityManager->getRepository(Marker::class)->findBy(['letter' => $letter]);
    }

    /**
     * @param string $color
     * @return Marker[]
     */
    public function findMarkersByColor(string $color): array
    {
        return $this->entityManager->getRepository(Marker::class)->findBy(['color' => $color]);
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
