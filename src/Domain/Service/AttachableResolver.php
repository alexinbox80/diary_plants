<?php

namespace App\Domain\Service;

use App\Domain\Entity\Plant;
use App\Domain\Entity\Offspring;
use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Repository\AttachableResolverInterface;

class AttachableResolver implements AttachableResolverInterface
{
    private array $classMap = [
        'plant::class' => Plant::class,
        'offspring::class' => Offspring::class
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function resolve(string $type, int $id): ?AttachableInterface
    {
        $class = $this->classMap[$type] ?? null;

        if (!isset($class)) {
            throw new \InvalidArgumentException("Unsupported attachable type: {$type}");
        }

        $entity = $this->entityManager->find($class, $id);

        return $entity instanceof AttachableInterface ? $entity : null;
    }
}
