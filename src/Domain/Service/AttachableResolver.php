<?php

namespace App\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\ValueObject\Enum\AttachableType;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Repository\AttachableResolverInterface;

class AttachableResolver implements AttachableResolverInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function resolve(string $type, int $id): ?AttachableInterface
    {
        $class = AttachableType::getClass($type);

        if (!isset($class)) {
            throw new \InvalidArgumentException("Unsupported attachable type: {$type}");
        }

        $entity = $this->entityManager->find($class, $id);

        return $entity instanceof AttachableInterface ? $entity : null;
    }
}
