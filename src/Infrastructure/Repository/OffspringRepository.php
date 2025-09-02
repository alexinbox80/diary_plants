<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Offspring;

class OffspringRepository extends AbstractRepository
{
    /**
     * @return Offspring[]
     */
    public function getOffspringsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Offspring::class, 'o')
            ->orderBy('o.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $offspringId
     * @return Offspring|null
     */
    public function find(int $offspringId): ?Offspring
    {
        $repository = $this->entityManager->getRepository(Offspring::class);
        /** @var Offspring|null $offspring */
        $offspring = $repository->find($offspringId);

        return $offspring;
    }

    /**
     * @return Offspring[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Offspring::class)->findAll();
    }

    /**
     * @param string $mass
     * @return Offspring[]
     */
    public function findOffspringsByMass(string $mass): array
    {
        return $this->entityManager->getRepository(Offspring::class)->findBy(['mass' => $mass]);
    }

    /**
     * @param string $flavor
     * @return Offspring[]
     */
    public function findOffspringsByFlavor(string $flavor): array
    {
        return $this->entityManager->getRepository(Offspring::class)->findBy(['flavor' => $flavor]);
    }

    /**
     * @param string $color
     * @return Offspring[]
     */
    public function findOffspringsByColor(string $color): array
    {
        return $this->entityManager->getRepository(Offspring::class)->findBy(['color' => $color]);
    }

    /**
     * @param  Offspring $offspring
     * @return int
     */
    public function create(Offspring $offspring): int
    {
        return $this->store($offspring);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Offspring $offspring
     * @return void
     */
    public function remove(Offspring $offspring): void
    {
        $offspring->setDeletedAt();
        $this->flush();
    }
}
