<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Pest;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;

class PestRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('p', 'g', 'm')
            ->from(Pest::class, 'p')
            ->leftJoin('p.group', 'g')
            ->leftJoin('p.marker', 'm')
            ->orderBy('p.updatedAt', 'DESC');
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return Pest[]
     * @throws \Exception
     */
    public function getPestsPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $pestId
     * @return Pest|null
     */
    public function find(int $pestId): ?Pest
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('p.id = :id')
            ->setParameter('id', $pestId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Pest[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $title
     * @return Pest[]
     */
    public function findPestsByTitle(string $title): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('p.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $manufacturer
     * @return Pest[]
     */
    public function findPestsByManufacturer(string $manufacturer): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('p.manufacturer = :manufacturer')
            ->setParameter('manufacturer', $manufacturer)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTimeImmutable $date
     * @return Pest[]
     */
    public function findPestsByUseDate(DateTimeImmutable $date): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('p.useDate = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
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
