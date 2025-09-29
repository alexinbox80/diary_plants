<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Interfaces\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query;
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
     * @param Query $query
     * @param int $page
     * @param int $perPage
     * @return array{plants: object[], pagination: array}
     */
    protected function getPaginatedResults(Query $query, int $page, int $perPage): array
    {
        $doctrinePaginator = new DoctrinePaginator($query);
        $doctrinePaginator->setUseOutputWalkers(true);

        $offset = ($page - 1) * $perPage;
        $doctrinePaginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($perPage);

        $totalItems = count($doctrinePaginator);
        $totalPages = (int) ceil($totalItems / $perPage);

        return [
            'items' => $doctrinePaginator->getQuery()->getResult(),
            'pagination' => [
                'first' => 1 + $offset,
                'last' => min($perPage + $offset, $totalItems),
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
        self::flush();

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
