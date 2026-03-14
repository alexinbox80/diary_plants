<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Marker;
use App\Domain\Entity\Stimulant;
use DateTimeImmutable;

class StimulantRepository extends AbstractRepository
{
    /**
     * @return Stimulant[]
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Stimulant::class, 's')
            ->orderBy('s.updatedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage)
            ->getQuery();

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $stimulantId
     * @return Stimulant|null
     */
    public function find(int $stimulantId): ?Stimulant
    {
        $repository = $this->entityManager->getRepository(Stimulant::class);
        /** @var Stimulant|null $stimulant */
        $stimulant = $repository->find($stimulantId);

        return $stimulant;
    }

    /**
     * @return Stimulant[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Stimulant::class)->findAll();
    }

    /**
     * @param string $title
     * @return Stimulant[]
     */
    public function findStimulantsByTitle(string $title): array
    {
        return $this->entityManager->getRepository(Stimulant::class)->findBy(['title' => $title]);
    }

    /**
     * @param string $manufacturer
     * @return Stimulant[]
     */
    public function findStimulantsByManufacturer(string $manufacturer): array
    {
        return $this->entityManager->getRepository(Stimulant::class)->findBy(['manufacturer' => $manufacturer]);
    }

    /**
     * @param DateTimeImmutable $date
     * @return Stimulant[]
     */
    public function findStimulantsByUseDate(DateTimeImmutable $date): array
    {
        return $this->entityManager->getRepository(Stimulant::class)->findBy(['use_date' => $date]);
    }

    /**
     * @param Stimulant $stimulant
     * @return int
     */
    public function create(Stimulant $stimulant): int
    {
        return $this->store($stimulant);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Stimulant $stimulant
     * @return void
     */
    public function remove(Stimulant $stimulant): void
    {
        $stimulant->setDeletedAt();
        $this->flush();
    }
}
