<?php

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Interfaces\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;

/**
 * @template T
 */
abstract class AbstractRepository
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
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
