<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Stimulant;
use DateTime;

class StimulantRepository extends AbstractRepository
{
    /**
     * @return Stimulant[]
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Stimulant::class, 't')
            ->orderBy('t.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
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
     * @param DateTime $date
     * @return Stimulant[]
     */
    public function findStimulantsByUseDate(DateTime $date): array
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
