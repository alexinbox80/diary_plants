<?php

namespace App\Infrastructure\Repository;

use Exception;
use RuntimeException;
use DateTimeImmutable;
use App\Domain\Entity\Usage;
use App\Domain\Entity\Plant;
use InvalidArgumentException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Common\Collections\ArrayCollection;
use App\Domain\ValueObject\Enum\Usage\AttachableType;

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
            ->leftJoin('p.analytic', 'a')
            ->addSelect('a')
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
     * Инкапсулирует логику загрузки и распределения вложений
     * @param Usage[] $usages
     * @return void
     */
    public function getRelatedMapForUsages(array $usages): void
    {
        if (empty($usages)) return;

        $groupedIds = [];
        foreach ($usages as $usage) {
            $target = $usage->getTarget();
            $groupedIds[$target->getUsableType()->value][] = $target->getUsableId();
        }

        $loadedEntities = [];
        foreach ($groupedIds as $type => $ids) {
            $entityClass = AttachableType::getClass($type);
            if (!$entityClass) continue;

            $entities = $this->entityManager->getRepository($entityClass)
                ->findBy(['id' => array_unique($ids)]);

            foreach ($entities as $entity) {
                // Кэшируем: [тип][id_сущности] = Объект
                $loadedEntities[$type][$entity->getId()] = $entity;
            }
        }

        foreach ($usages as $usage) {
            $target = $usage->getTarget();
            $type = $target->getUsableType()?->value;
            $id = $target->getUsableId();

            // Достаем один объект по ключам типа и ID
            $relatedObject = $loadedEntities[$type][$id] ?? null;

            // Устанавливаем как одиночный объект
            $usage->setLoadedAttachment($relatedObject);
        }
    }

    /**
     * @param Plant $plant
     * @return void
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadTopUsagesForPlant(Plant $plant): void
    {
        $sql = '
        SELECT id FROM (
            SELECT id,
                   ROW_NUMBER() OVER (PARTITION BY usable_type ORDER BY created_at DESC) as rn
            FROM usage
            WHERE plant_id = :plantId
              AND usable_type IN (\'fertilizer\', \'pest\', \'stimulant\', \'watering\')
        ) t
        WHERE t.rn <= 5
        ';

        $ids = $this->entityManager->getConnection()->fetchFirstColumn($sql, [
            'plantId' => $plant->getId()
        ]);

        $collection = $plant->getUsages();

        if (!empty($ids)) {
            // Подгружаем сами сущности по найденным ID и устанавливаем в коллекцию
            $usages = $this->entityManager->getRepository(Usage::class)
                ->findBy(['id' => $ids], ['useDate' => 'DESC']);

            $collection->clear();
            foreach ($usages as $usage) {
                $collection->add($usage);
            }

            // Помечаем коллекцию как "загруженную", чтобы Doctrine не полезла в базу снова
            $collection->setInitialized(true);
        } else
            $collection->setInitialized(true);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @return Usage[]
     */
    public function findBy(array $criteria, array $orderBy): array
    {
        return $this->entityManager
            ->getRepository(Usage::class)
            ->findBy($criteria, $orderBy);
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
     * Получить последнюю дату использования до указанной даты.
     * @param int $groupId
     * @param int $usableId
     * @param string $usableType
     * @param DateTimeImmutable $date
     * @return DateTimeImmutable|null
     */
    public function findLatestDateBefore(int $groupId, int $usableId, string $usableType, DateTimeImmutable $date): ?DateTimeImmutable
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb->select('u.useDate')
            ->from(Usage::class, 'u')
            ->where('u.group = :groupId')
//            ->andWhere('u.target.usableId = :usableId')
            ->andWhere('u.target.usableType = :usableType')
            ->andWhere('u.useDate < :date')
            ->setParameter('groupId', $groupId)
//            ->setParameter('usableId', $usableId)
            ->setParameter('usableType', $usableType)
            ->setParameter('date', $date)
            ->orderBy('u.useDate', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()['useDate'] ?? null;
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
     * @throws Exception
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
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return Usage[]
     * @throws Exception
     */
    public function getUsagesPaginatedByGroupId(int $page, int $perPage, ?int $groupId = null): array
    {
        $queryBuilder = $this->getBaseQueryBuilder();

        $qb = $queryBuilder
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($groupId !== null) {
            $qb->where('u.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return $this->getPaginatedResults($queryBuilder, $page, $perPage);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @param int|null $groupId
     * @return Usage[]
     *
     * @throws Exception
     */
    public function getUsagesPaginatedByGroupIdWithAttachments(int $page, int $perPage, ?int $groupId = null): array
    {
        $result = $this->getUsagesPaginatedByGroupId($page, $perPage, $groupId);

        if (!isset($result['items'])) {
            throw new RuntimeException('Pagination result is missing "items".');
        }

        if (!is_array($result['items'])) {
            throw new InvalidArgumentException('"items" must be an array.');
        }

        $this->preloadTargets($result['items']);

        return $result;
    }

    /**
     * @return Usage[]
     * @throws Exception
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
     * @param int|null $groupId
     * @return Usage[]
     */
    public function findAllByGroupId(?int $groupId = null): array
    {
        $queryBuilder = $this->getBaseQueryBuilder();

        $qb = $queryBuilder;

        if ($groupId !== null) {
            $qb->where('u.group = :groupId')
                ->setParameter('groupId', $groupId);
        }

        return  $qb->getQuery()->getResult();
    }

    /**
     * @param int|null $groupId
     * @return Usage[]
     */
    public function findAllWithAttachments(?int $groupId = null): array
    {
        $usages = $this->findAllByGroupId($groupId);

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
     * @param array $ids
     * @return array
     */
    public function findPlantIdsByIds(array $ids): array
    {
        $results = $this->entityManager->createQueryBuilder('u')
            ->select('DISTINCT IDENTITY(u.plant) as plantId')
            ->from(Usage::class, 'u')
            ->where('u.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'plantId');
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
