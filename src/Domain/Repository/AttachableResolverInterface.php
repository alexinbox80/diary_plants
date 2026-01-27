<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Interfaces\AttachableInterface;

interface AttachableResolverInterface
{
    public function resolve(string $type, int $id): ?AttachableInterface;
}
