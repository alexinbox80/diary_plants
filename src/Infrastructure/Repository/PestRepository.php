<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pest;
use DateTimeImmutable;

class PestRepository extends AbstractRepository
{
    /**
     * @return Pest[]
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('p')
            ->from(Pest::class, 'p')
            ->orderBy('p.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $pestId
     * @return Pest|null
     */
    public function find(int $pestId): ?Pest
    {
        $repository = $this->entityManager->getRepository(Pest::class);
        /** @var Pest|null $pest */
        $pest = $repository->find($pestId);

        return $pest;
    }

    /**
     * @return Pest[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Pest::class)->findAll();
    }

    /**
     * @param string $title
     * @return Pest[]
     */
    public function findPestsByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Pest::class)->findBy(['title' => $title]);
    }

    /**
     * @param string $manufacturer
     * @return Pest[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        return $this->entityManager->getRepository(Pest::class)->findBy(['manufacturer' => $manufacturer]);
    }

    /**
     * @param DateTimeImmutable $date
     * @return Pest[]
     */
    public function findPestsByUseDate(DateTimeImmutable $date): array
    {
        return $this->entityManager->getRepository(Pest::class)->findBy(['use_date' => $date]);
    }

    /**
     * @param Pest $pest
     * @return int
     */
    public function create(Pest $pest): int
    {
        return $this->store($pest);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Pest $pest
     * @return void
     */
    public function remove(Pest $pest): void
    {
        $pest->setDeletedAt();
        $this->flush();
    }
}
