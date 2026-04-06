<?php

namespace App\Infrastructure\Repository;

use DateTimeImmutable;
use App\Domain\Entity\Usage;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

/**
 * @method Usage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsageRepository extends AbstractRepository
{
    /**
     * @return QueryBuilder
     */
    private function getBaseQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder->select('u', 'g', 'p')
            ->from(Usage::class, 'u')
            ->leftJoin('u.group', 'g')
            ->leftJoin('u.plant', 'p')
            ->orderBy('u.updatedAt', 'DESC');
    }

    /**
     * @param Usage[] $usages
     * @return void
     */
    private function preloadTargets(array $usages): void
    {
        $map = [];
        foreach ($usages as $usage) {
            $target = $usage->getTarget();
            // Наш Enum из поля attachableType
            $type = $target->getUsableType();
            if ($type instanceof AttachableType) {
                $map[$type->value][] = $target->getUsableId();
            }
        }

        foreach ($map as $typeAlias => $ids) {
            $ids = array_unique(array_filter($ids));
            if (empty($ids)) continue;

            $enumCase = AttachableType::from($typeAlias);
            $className = $enumCase->getClass($enumCase->value);

            // Создаем запрос вручную, чтобы добавить JOIN маркера
            $this->entityManager->getRepository($className)
                ->createQueryBuilder('t')
                ->select('t', 'm')            // Выбираем и цель, и маркер
                ->leftJoin('t.marker', 'm')   // Сразу джоиним маркер
                ->where('t.id IN (:ids)')
                // Важно: если используете SoftDelete, лучше добавить это условие явно,
                // чтобы избежать создания лишних Proxy для удаленных записей
                ->andWhere('t.deletedAt IS NULL')
                ->setParameter('ids', $ids)
                ->getQuery()
                ->getResult();
        }
    }

    /**
     * Получить все использования для определённой сущности.
     *
     * @param string $usableType Например 'App\Domain\Entity\Usage'
     * @param int $usableId
     * @return array
     */
    public function findByUsable(string $usableType, int $usableId): array
    {
        return $this->getBaseQueryBuilder()
            ->where('u.target.usableType = :type')
            ->andWhere('u.target.usableId = :id')
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
        return $this->getBaseQueryBuilder()
            ->where('u.target.usableType = :type')
            ->andWhere('u.target.usableId = :id')
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
        return $this->getBaseQueryBuilder()
            ->where('u.target.usableType = :type')
            ->andWhere('u.target.usableId = :id')
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
        $this->getBaseQueryBuilder()
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
     * @throws \Exception
     */
    public function getUsagesPaginated(int $page, int $perPage): array
    {
        $queryBuilder = $this->getBaseQueryBuilder()
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $results = $this->getPaginatedResults($queryBuilder, $page, $perPage);

        $this->preloadTargets($results['items']);

        return $results;
    }

    /**
     * @return Usage[]
     * @throws \Exception
     */
    public function getUsages(int $year, int $month, int $groupId): array
    {
        $startDate = new DateTimeImmutable("$year-$month-01 00:00:00");
        $endDate = $startDate->modify('last day of this month')->setTime(23, 59, 59);

        return $this->getBaseQueryBuilder()
            ->where('u.group = :groupId')
            ->andWhere('u.useDate BETWEEN :start AND :end')
            ->setParameter('groupId', $groupId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate)
            ->orderBy('u.useDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $groupId
     * @param array $plantIds
     * @param array $dates
     * @return Usage[]
     */
    public function findExistingKeys(int $groupId, array $plantIds, array $dates): array
    {
        $parameters = new ArrayCollection([
            new Parameter('groupId', $groupId),
            new Parameter('plantIds', $plantIds),
            new Parameter('dates', $dates),
        ]);

        $qb = $this->entityManager->createQueryBuilder('u');

        return $qb->select('IDENTITY(u.group) as groupId, IDENTITY(u.plant) as plantId, u.useDate, u.target.usableType as usableType, u.target.usableId as usableId')
            ->from(Usage::class, 'u')
            ->where('u.group = :groupId')
            ->andWhere('u.plant IN (:plantIds)')
            ->andWhere('u.useDate IN (:dates)')
            ->setParameters($parameters)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $usageId
     * @return Usage|null
     */
    public function find(int $usageId): ?Usage
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.id = :id')
            ->setParameter('id', $usageId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Usage[]
     */
    public function findAll(): array
    {
        return $this->getBaseQueryBuilder()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Usage[]
     */
    public function findAllWithTargets(): array
    {
        $usages = $this->findAll();

        $this->preloadTargets($usages);

        return $usages;
    }

    /**
     * @param DateTimeImmutable $useDate
     * @return Usage[]
     */
    public function findUsagesByUseDate(DateTimeImmutable $useDate): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.useDate = :useDate')
            ->setParameter('useDate', $useDate)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $usableId
     * @return Usage[]
     */
    public function findUsagesByUsableId(int $usableId): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.usableId = :usableId')
            ->setParameter('usableId', $usableId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $usableType
     * @return Usage[]
     */
    public function findUsagesByUsableType(string $usableType): array
    {
        return $this->getBaseQueryBuilder()
            ->andWhere('u.usableType = :usableType')
            ->setParameter('usableType', $usableType)
            ->getQuery()
            ->getResult();
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

    /**
     * @param array $ids
     * @param int $groupId
     * @return int
     */
    public function removeByIds(array $ids, int $groupId): int
    {
        return $this->entityManager->createQueryBuilder('u')
            ->delete(Usage::class, 'u')
            ->where('u.id IN (:ids)')
            ->andWhere('u.group = :groupId')
            ->setParameter('ids', $ids)
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->execute();
    }
}
