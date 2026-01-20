<?php

namespace App\Domain\Entity\Interfaces;

use DateTimeImmutable;

interface SoftDeletableInterface
{
    public function getDeletedAt(): ?DateTimeImmutable;

    public function setDeletedAt(): void;
}
