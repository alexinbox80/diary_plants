<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Interfaces\AttachableInterface;
use App\Domain\ValueObject\Enum\Usage\AttachableType as AttachableTypeUsage;
use App\Domain\ValueObject\Enum\Attachment\AttachableType as AttachableTypeAttachment;

interface AttachableResolverInterface
{
    public function resolve(AttachableTypeAttachment|AttachableTypeUsage $type, int $id): ?AttachableInterface;
}
