<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Usage;
use DateTimeImmutable;

/**
 * @method Usage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsageRepository extends AbstractRepository
{
    /**
     * Получить все использования для определённой сущности.
     *
     * @param string $usableType Например 'App\Domain\Entity\Usage'
     * @param int $usableId
     * @return array
     */
    public function findByUsable(string $usableType, int $usableId): array
    {
        return $this->entityManager->createQueryBuilder('u')
            ->andWhere('u.usableType = :type')
            ->andWhere('u.usableId = :id')
            ->setParameter('type', $usableType)
            ->setParameter('id', $usableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить все использования для определённой сущности, включая удалённые.
     */
    public function findByUsableWithDeleted(string $usableType, int $usableId): array
    {
        return $this->entityManager->createQueryBuilder('u')
            ->andWhere('u.usableType = :type')
            ->andWhere('u.usableId = :id')
            ->setParameter('type', $usableType)
            ->setParameter('id', $usableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Получить использования по ID и типу сущности.
     */
    public function findOneByUsable(string $usableType, int $usableId, int $usageId): ?Usage
    {
        return $this->entityManager->createQueryBuilder('u')
            ->andWhere('u.usableType = :type')
            ->andWhere('u.usableId = :id')
            ->andWhere('u.id = :usageId')
            ->setParameter('type', $usableType)
            ->setParameter('id', $usableId)
            ->setParameter('usageId', $usageId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Удалить использование по ID и типу сущности.
     */
    public function deleteByUsable(string $usableType, int $usableId, int $usageId): void
    {
        $this->entityManager->createQueryBuilder('u')
            ->delete()
            ->andWhere('u.usableType = :type')
            ->andWhere('u.usableId = :id')
            ->andWhere('u.id = :usageId')
            ->setParameter('type', $usableType)
            ->setParameter('id', $usableId)
            ->setParameter('usageId', $usageId)
            ->getQuery()
            ->execute();
    }

    /**
     * @return Usage[]
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('s')
            ->from(Usage::class, 'u')
            ->orderBy('u.id', 'DESC')
            ->setFirstResult($perPage * $page)
            ->setMaxResults($perPage);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param int $usageId
     * @return Usage|null
     */
    public function find(int $usageId): ?Usage
    {
        $repository = $this->entityManager->getRepository(Usage::class);
        /** @var Usage|null $usage */
        $usage = $repository->find($usageId);

        return $usage;
    }

    /**
     * @return Usage[]
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Usage::class)->findAll();
    }

    /**
     * @param DateTimeImmutable $useDate
     * @return Usage[]
     */
    public function findUsagesByUseDate(DateTimeImmutable $useDate): array
    {
        return $this->entityManager->getRepository(Usage::class)->findBy(['use_date' => $useDate]);
    }

    /**
     * @param int $usableId
     * @return Usage[]
     */
    public function findUsagesByUsableId(int $usableId): array
    {
        return $this->entityManager->getRepository(Usage::class)->findBy(['usable_id' => $usableId]);
    }

    /**
     * @param string $usableType
     * @return Usage[]
     */
    public function findUsagesByUsableType(string $usableType): array
    {
        return $this->entityManager->getRepository(Usage::class)->findBy(['usable_type' => $usableType]);
    }

    /**
     * @param Usage $usage
     * @return int
     */
    public function create(Usage $usage): int
    {
        return $this->store($usage);
    }

    /**
     * @return void
     */
    public function update(): void
    {
        $this->flush();
    }

    /**
     * @param Usage $usage
     * @return void
     */
    public function remove(Usage $usage): void
    {
        $usage->setDeletedAt();
        $this->flush();
    }
}
