<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use App\Domain\Entity\Stimulant;

class StimulantRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('s', 'g', 'm')
            ->from(Stimulant::class, 's')
            ->leftJoin('s.group', 'g')
            ->leftJoin('s.marker', 'm')
            ->orderBy('s.updatedAt', 'DESC');
    }

    /**
     * @return Stimulant[]
     * @throws \Exception
     */
    public function getStimulantsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $stimulantId
     * @return Stimulant|null
     */
    public function find(int $stimulantId): ?Stimulant
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('s.id = :id')
            ->setParameter('id', $stimulantId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Stimulant[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $title
     * @return Stimulant[]
     */
    public function findStimulantsByTitle(string $title): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('s.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $manufacturer
     * @return Stimulant[]
     */
    public function findStimulantsByManufacturer(string $manufacturer): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('s.manufacturer = :manufacturer')
            ->setParameter('manufacturer', $manufacturer)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $date
     * @return Stimulant[]
     */
    public function findStimulantsByUseDate(DateTimeImmutable $date): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('s.use_date = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
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
