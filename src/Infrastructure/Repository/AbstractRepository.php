<?php

namespace App\Infrastructure\Repository;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Domain\Entity\Interfaces\EntityInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @template T
 */
abstract class AbstractRepository
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Получает данные с пагинацией.
     *
     * @param QueryBuilder $query
     * @param int $page
     * @param int $perPage
     * @return array{plants: object[], pagination: array}
     * @throws \Exception
     */
    protected function getPaginatedResults(QueryBuilder $query, int $page, int $perPage): array
    {
        $query->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $doctrinePaginator = new DoctrinePaginator($query, true);

        $doctrinePaginator->setUseOutputWalkers(true);

        $totalItems = count($doctrinePaginator);
        $totalPages = (int) ceil($totalItems / $perPage);
        $offset = ($page - 1) * $perPage;

        $items = iterator_to_array($doctrinePaginator->getIterator());

        return [
            'items' => $items,
            'pagination' => [
                'first' => $totalItems > 0 ? $offset + 1 : 0,
                'last' => min($offset + $perPage, $totalItems),
                'total' => $totalItems,
                'current' => $page,
                'pages' => $totalPages,
                'count' => $perPage,
            ]
        ];
    }

    protected function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param EntityInterface $entity
     * @return int
     */
    protected function store(EntityInterface $entity): int
    {
        $this->entityManager->persist($entity);
        $this->flush();

        return $entity->getId();
    }

    /**
     * @param EntityInterface $entity
     * @throws ORMException
     */
    public function refresh(EntityInterface $entity): void
    {
        $this->entityManager->refresh($entity);
    }
}
