<?php

namespace App\Domain\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\Repository\AttachableResolverInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType as AttachableTypeUsage;
use App\Domain\ValueObject\Enum\Attachment\AttachableType as AttachableTypeAttachment;

class AttachableResolver implements AttachableResolverInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function resolve(AttachableTypeAttachment|AttachableTypeUsage $type, int $id): ?AttachableInterface
    {
        $class = $type->getClass($type->value);

        if ($class === null) {
            throw new \InvalidArgumentException("Unsupported attachable type: {$type->value}");
        }

        $entity = $this->entityManager->find($class, $id);

        return $entity instanceof AttachableInterface ? $entity : null;
    }
}
